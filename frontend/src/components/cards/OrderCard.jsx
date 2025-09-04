"use client"

import styles from "./OrderCard.module.css"

export default function OrderCard({ order, onClick, payment=false }) {
  const handleCardClick = () => {
    if (payment) {
      onClick(order.id)
    } else {
      onClick()
    }
  }

  const getStatusClass = (status) => {
    switch (status.toLowerCase()) {
      case "pending":
        return styles.pending
      case "preparing":
        return styles.preparing
      case "ready":
        return styles.ready
      case "delivered":
        return styles.delivered
      case "cancelled":
        return styles.cancelled
      default:
        return styles.default
    }
  }

  const getPaymentStatusClass = (status) => {
    switch (status.toLowerCase()) {
      case "paid":
        return styles.paid
      case "pending":
        return styles.paymentPending
      case "failed":
        return styles.failed
      default:
        return styles.default
    }
  }

  const formatOrderDate = (dateString) => {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  };

  return (
    <div className={styles.card} onClick={handleCardClick}>
      <div className={styles.header}>
        <h3 className={styles.orderId}>Order #{order.id}</h3>
        <div className={styles.amount}>${Number.parseFloat(order.total_amount).toFixed(2)}</div>
      </div>

      <div className={styles.content}>
        <div className={styles.detail}>
          <span className={styles.label}>User ID:</span>
          <span className={styles.value}>{order.user_id}</span>
        </div>

        <div className={styles.detail}>
          <span className={styles.label}>Order Date:</span>
          <span className={styles.value}>{formatOrderDate(order.order_date)}</span>
        </div>

        <div className={styles.statusRow}>
          <div className={styles.statusItem}>
            <span className={styles.label}>Status:</span>
            <span className={`${styles.status} ${getStatusClass(order.order_status)}`}>{order.order_status}</span>
          </div>

          <div className={styles.statusItem}>
            <span className={styles.label}>Payment:</span>
            <span className={`${styles.paymentStatus} ${getPaymentStatusClass(order.payment_status)}`}>
              {order.payment_status}
            </span>
          </div>

          {
            payment && (
              <div className={styles.statusItem}>
                <span className={styles.label}>Confirm:</span>
                <span className={`${styles.paymentStatus} ${styles.payment}`}>
                  Confirm Payment
                </span>
              </div>
            )
          }
        </div>
      </div>
    </div>
  )
}

