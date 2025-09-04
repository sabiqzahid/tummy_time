"use client"
import AddFeedbackForm from "@/components/forms/AddFeedbackForm"
import styles from "./AddFeedbackModal.module.css"

export default function AddFeedbackModal({ foodId, onClose, onSuccess }) {
  return (
    <div className={styles.overlay} onClick={onClose}>
      <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
        <div className={styles.header}>
          <h2 className={styles.title}>Add Feedback</h2>
          <button onClick={onClose} className={styles.closeButton}>
            ×
          </button>
        </div>

        <AddFeedbackForm foodId={foodId} onSuccess={onSuccess} onCancel={onClose} />
      </div>
    </div>
  )
}
