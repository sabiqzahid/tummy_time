"use client"

import { useState } from "react"
import { updateFeedbackAction } from "@/actions/feedbackActions"
import styles from "./UpdateFeedbackModal.module.css"

export default function UpdateFeedbackModal({ feedback, onClose, onSuccess }) {
  const [comment, setComment] = useState(feedback.comment)
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState("")

  const handleSubmit = async (e) => {
    e.preventDefault()
    setIsLoading(true)
    setError("")

    try {
      const result = await updateFeedbackAction(feedback.id, { comment })
      if (result.success) {
        onSuccess()
      } else {
        setError(result.error || "Failed to update feedback")
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
          <h3 className={styles.title}>Update Feedback</h3>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <form onSubmit={handleSubmit} className={styles.form}>
          <div className={styles.field}>
            <label htmlFor="comment" className={styles.label}>
              Your Feedback
            </label>
            <textarea
              id="comment"
              value={comment}
              onChange={(e) => setComment(e.target.value)}
              className={styles.textarea}
              rows={4}
              required
              disabled={isLoading}
            />
          </div>

          {error && <div className={styles.error}>{error}</div>}

          <div className={styles.actions}>
            <button type="button" onClick={onClose} className={styles.cancelButton} disabled={isLoading}>
              Cancel
            </button>
            <button type="submit" className={styles.submitButton} disabled={isLoading || !comment.trim()}>
              {isLoading ? "Updating..." : "Update Feedback"}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}
