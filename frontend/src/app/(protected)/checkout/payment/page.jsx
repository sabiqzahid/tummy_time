"use client"
import { useState } from "react"
import { useRouter } from "next/navigation"
import { createOrderAction } from "@/actions/orderActions"
import { createPaymentAction } from "@/actions/paymentActions"
import styles from "./page.module.css"

export default function PaymentPage() {
  const [formData, setFormData] = useState({
    cardNumber: "",
    expiryDate: "",
    cvv: "",
    cardholderName: "",
  })
  const [loading, setLoading] = useState(false)
  const [success, setSuccess] = useState("")
  const [error, setError] = useState("")
  const router = useRouter()

  const handleInputChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError("")

    try {
      const orderResult = await createOrderAction()

      if (orderResult.error) {
        setError(orderResult.error.error)
        return
      }

      const paymentFormData = new FormData()
      console.log("orderResult", orderResult)
      paymentFormData.append("order", orderResult?.order_id || "1")
      paymentFormData.append("payment", "card")

      const paymentResult = await createPaymentAction(paymentFormData)

      if (paymentResult.error) {
        setError(paymentResult.error.error)
        return
      }

      setSuccess("Order created and Payment processed successfully!")
      setTimeout(() => {
        router.push("/orders")
      }, 1500)
    } catch (err) {
      setError("Payment processing failed")
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Payment Details</h1>
        <p className={styles.subtitle}>Enter your card information to complete the order</p>
      </div>

      {success && <div className={styles.success}>{success}</div>}
      {error && <div className={styles.error}>{error}</div>}

      <form onSubmit={handleSubmit} className={styles.form}>
        <div className={styles.field}>
          <label className={styles.label}>Cardholder Name</label>
          <input
            type="text"
            name="cardholderName"
            value={formData.cardholderName}
            onChange={handleInputChange}
            className={styles.input}
            placeholder="John Doe"
            required
          />
        </div>

        <div className={styles.field}>
          <label className={styles.label}>Card Number</label>
          <input
            type="text"
            name="cardNumber"
            value={formData.cardNumber}
            onChange={handleInputChange}
            className={styles.input}
            placeholder="1234 5678 9012 3456"
            maxLength="19"
            required
          />
        </div>

        <div className={styles.row}>
          <div className={styles.field}>
            <label className={styles.label}>Expiry Date</label>
            <input
              type="text"
              name="expiryDate"
              value={formData.expiryDate}
              onChange={handleInputChange}
              className={styles.input}
              placeholder="MM/YY"
              maxLength="5"
              required
            />
          </div>

          <div className={styles.field}>
            <label className={styles.label}>CVV</label>
            <input
              type="text"
              name="cvv"
              value={formData.cvv}
              onChange={handleInputChange}
              className={styles.input}
              placeholder="123"
              maxLength="4"
              required
            />
          </div>
        </div>

        <div className={styles.actions}>
          <button type="button" className={styles.backButton} onClick={() => router.back()} disabled={loading}>
            Back
          </button>

          <button type="submit" className={styles.submitButton} disabled={loading}>
            {loading ? "Processing..." : "Complete Order"}
          </button>
        </div>
      </form>
    </div>
  )
}

