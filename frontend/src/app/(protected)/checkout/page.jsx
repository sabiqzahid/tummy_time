"use client"
import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { createOrderAction } from "@/actions/orderActions"
import { getCartItemsAction } from "@/actions/cartActions"
import { getUserIdAction } from "@/actions/authActions"
import styles from "./page.module.css"

export default function CheckoutPage() {
  const [cartItems, setCartItems] = useState([])
  const [loading, setLoading] = useState(true)
  const [success, setSuccess] = useState("")
  const [error, setError] = useState("")
  const router = useRouter()

  useEffect(() => {
    setSuccess("")
    setError("")
    loadCartItems()
  }, [])

  const loadCartItems = async () => {
    try {
      setLoading(true)
      const userId = await getUserIdAction()
      if (!userId) {
        router.push("/login")
        return
      }

      const result = await getCartItemsAction(userId)

      if (result.error) {
        setError(result.error)
      } else {
        // Fix is here: access the cart_items array from the nested data object
        setCartItems(result.data.cart_items || [])
      }
    } catch (err) {
      setError("Failed to load cart items")
    } finally {
      setLoading(false)
    }
  }

  const calculateTotal = () => {
    return cartItems
      .reduce((total, item) => {
        const price = Number.parseFloat(item.food.price)
        return total + price * item.quantity
      }, 0)
      .toFixed(2)
  }

  const handlePaymentMethod = (method) => {
    if (method === "card") {
      router.push("/checkout/payment")
    } else if (method === "cash") {
      handleCashPayment()
    }
  }

  const handleCashPayment = async () => {
    try {
      const result = await createOrderAction()

      if (result.success) {
        setSuccess("Order created successfully!")
        setTimeout(() => {
          router.push("/orders")
        }, 1500)
      } else if (result.error) {
        setError(result.error.error)
      }
    } catch (err) {
      setError("Failed to process order")
    }
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading checkout...</div>
      </div>
    )
  }

  if (cartItems.length === 0) {
    return (
      <div className={styles.container}>
        <div className={styles.emptyCart}>
          <h2>Your cart is empty</h2>
          <button className={styles.shopButton} onClick={() => router.push("/foods")}>
            Browse Foods
          </button>
        </div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <button onClick={() => router.back()} className={styles.backButton}>
          ← Back
        </button>
        <h1 className={styles.title}>Checkout</h1>
      </div>

      {success && <div className={styles.success}>{success}</div>}
      {error && <div className={styles.error}>{error}</div>}

      <div className={styles.orderSummary}>
        <h2 className={styles.sectionTitle}>Order Summary</h2>
        <div className={styles.items}>
          {cartItems.map((item) => (
            <div key={item.id} className={styles.item}>
              <span className={styles.itemName}>{item.food.name}</span>
              <span className={styles.itemQuantity}>x{item.quantity}</span>
              <span className={styles.itemPrice}>
                ${(Number.parseFloat(item.food.price) * item.quantity).toFixed(2)}
              </span>
            </div>
          ))}
        </div>
        <div className={styles.total}>
          <strong>Total: ${calculateTotal()}</strong>
        </div>
      </div>

      <div className={styles.paymentMethods}>
        <h2 className={styles.sectionTitle}>Payment Method</h2>
        <div className={styles.methodButtons}>
          <button className={styles.methodButton} onClick={() => handlePaymentMethod("card")}>
            <div className={styles.methodIcon}>💳</div>
            <div className={styles.methodText}>
              <h3>Card Payment</h3>
              <p>Pay with credit or debit card</p>
            </div>
          </button>

          <button className={styles.methodButton} onClick={() => handlePaymentMethod("cash")}>
            <div className={styles.methodIcon}>💵</div>
            <div className={styles.methodText}>
              <h3>Cash Payment</h3>
              <p>Pay when you receive your order</p>
            </div>
          </button>
        </div>
      </div>
    </div>
  )
}