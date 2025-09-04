"use client"

import styles from "./OrderDetailCard.module.css"
import { useState } from "react"
import { useRouter } from "next/navigation"
import { deleteOrderAction } from "@/actions/orderActions"

export default function OrderDetailCard({ order, isAdmin = false }) {
  const router = useRouter()
  const [success, setSuccess] = useState(null)
  const [error, setError] = useState(null)

  const formatOrderDate = (dateString) => {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case "pending":
        return styles.statusPending
      case "preparing":
        return styles.statusPreparing
      case "ready":
        return styles.statusReady
      case "delivered":
        return styles.statusDelivered
      case "cancelled":
        return styles.statusCancelled
      default:
        return styles.statusDefault
    }
  }

  const getPaymentStatusColor = (status) => {
    switch (status) {
      case "paid":
        return styles.paymentPaid
      case "pending":
        return styles.paymentPending
      case "failed":
        return styles.paymentFailed
      default:
        return styles.paymentDefault
    }
  }

  const onDelete = async () => {
    try {
      const result = await deleteOrderAction(order.id)

      if (result.error) {
        setError(result.error)
      } else {
        setSuccess("Order deleted successfully!")
        setTimeout(() => {
          router.push("/orders")
        }, 1500)
      }
    } catch (err) {
      setError("Failed to cancel order")
    }
  }

  return (
    <div className={styles.card}>

      {success && <div className={styles.success}>{success}</div>}
      {error && <div className={styles.error}>{error}</div>}

      <div className={styles.header}>
        <div className={styles.orderInfo}>
          <h2 className={styles.orderId}>Order #{order.id}</h2>
          <p className={styles.orderDate}>{formatOrderDate(order.order_date)}</p>
        </div>
        <div className={styles.totalAmount}>${order.total_amount}</div>
      </div>

      <div className={styles.statusSection}>
        <div className={styles.statusItem}>
          <span className={styles.statusLabel}>Order Status:</span>
          <span className={`${styles.statusBadge} ${getStatusColor(order.order_status)}`}>{order.order_status}</span>
        </div>
        <div className={styles.statusItem}>
          <span className={styles.statusLabel}>Payment Status:</span>
          <span className={`${styles.statusBadge} ${getPaymentStatusColor(order.payment_status)}`}>
            {order.payment_status}
          </span>
        </div>
      </div>

      <div className={styles.customerSection}>
        <h3 className={styles.sectionTitle}>Customer Information</h3>
        <div className={styles.customerInfo}>
          <div className={styles.customerDetail}>
            <span className={styles.label}>Customer ID:</span>
            <span className={styles.value}>{order.user_id}</span>
          </div>
          <div className={styles.customerDetail}>
            <span className={styles.label}>Username:</span>
            <span className={styles.value}>{order.user.username}</span>
          </div>
        </div>
      </div>

      <div className={styles.itemsSection}>
        <h3 className={styles.sectionTitle}>Order Items</h3>
        <div className={styles.orderItems}>
          {order.order_items.map((item) => (
            <div key={item.id} className={styles.orderItem}>
              <div className={styles.itemInfo}>
                <span className={styles.itemName}>{item.food.name}</span>
                <span className={styles.itemId}>Food ID: {item.food_id}</span>
              </div>
              <div className={styles.itemQuantity}>Qty: {item.quantity}</div>
            </div>
          ))}
        </div>
      </div>

      {isAdmin && (
        <div className={styles.actions}>
          <button onClick={onDelete} className={styles.deleteButton}>
            Delete Order
          </button>
        </div>
      )}
    </div>
  )
}

