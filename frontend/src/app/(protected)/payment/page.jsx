import { getPaymentsAction } from "@/actions/paymentActions"
import PaymentCard from "@/components/cards/PaymentCard"
import Link from "next/link"
import styles from "./page.module.css"

export default async function PaymentsPage() {
  const result = await getPaymentsAction()

  if (result.error) {
    return <div className={styles.error}>Error loading payments: {result.error}</div>
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1>Payment History</h1>
        <Link href="/" className={styles.backLink}>
          Back to Home
        </Link>
      </div>
      <div className={styles.confirmSection}>
        <Link href="/orders/pending" className={styles.confirmButton}>
          Confirm Order Payment
        </Link>
      </div>

      <div className={styles.paymentsGrid}>
        {result.data.map((payment) => (
          <PaymentCard key={payment.id} payment={payment} />
        ))}
      </div>
    </div>
  )
}
