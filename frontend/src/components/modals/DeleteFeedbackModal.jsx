"use client"

import { useState } from "react"
import { deleteFeedbackAction } from "@/actions/feedbackActions"
import styles from "./DeleteFeedbackModal.module.css"

export default function DeleteFeedbackModal({ feedback, onClose, onSuccess }) {
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState("")

  const handleDelete = async () => {
    setIsLoading(true)
    setError("")

    try {
      const result = await deleteFeedbackAction(feedback.id)
      if (result.success) {
        onSuccess()
      } else {
        setError(result.error || "Failed to delete feedback")
      }
    } catch (err) {
      setError("An unexpected error occurred")
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div className={styles.overlay} onClick={onClose}>
      <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
        <div className={styles.header}>
          <h3 className={styles.title}>Delete Feedback</h3>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <p className={styles.message}>Are you sure you want to delete this feedback? This action cannot be undone.</p>

          <div className={styles.feedbackPreview}>
            <p className={styles.previewText}>"{feedback.comment}"</p>
          </div>

          {error && <div className={styles.error}>{error}</div>}

          <div className={styles.actions}>
            <button type="button" onClick={onClose} className={styles.cancelButton} disabled={isLoading}>
              Cancel
            </button>
            <button onClick={handleDelete} className={styles.deleteButton} disabled={isLoading}>
              {isLoading ? "Deleting..." : "Delete Feedback"}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}
