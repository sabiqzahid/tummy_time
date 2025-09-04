"use client"

import { useState } from "react"
import {AddFeedbackButton,
UpdateFeedbackButton,
DeleteFeedbackButton }
from "@/components/buttons/buttons"
import AddFeedbackModal from "@/components/modals/AddFeedbackModal"
import UpdateFeedbackModal from "@/components/modals/UpdateFeedbackModal"
import DeleteFeedbackModal from "@/components/modals/DeleteFeedbackModal"
import styles from "./FeedbackSection.module.css"

export default function FeedbackSection({ foodId, feedbacks, currentUserId, onFeedbackChange, isAdmin=false }) {
  const [showAddModal, setShowAddModal] = useState(false)
  const [showUpdateModal, setShowUpdateModal] = useState(false)
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [selectedFeedback, setSelectedFeedback] = useState(null)

  const handleUpdateClick = (feedback) => {
    setSelectedFeedback(feedback)
    setShowUpdateModal(true)
  }

  const handleDeleteClick = (feedback) => {
    setSelectedFeedback(feedback)
    setShowDeleteModal(true)
  }

  const handleModalClose = () => {
    setShowAddModal(false)
    setShowUpdateModal(false)
    setShowDeleteModal(false)
    setSelectedFeedback(null)
  }

  const handleFeedbackSuccess = () => {
    onFeedbackChange()
    handleModalClose()
  }

  return (
    <div className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.title}>Customer Feedback</h2>
        {currentUserId && <AddFeedbackButton onClick={() => setShowAddModal(true)} />}
      </div>

      <div className={styles.feedbackList}>
        {feedbacks.length > 0 ? (
          feedbacks.map((feedback) => (
            <div key={feedback.id} className={styles.feedbackCard}>
              <div className={styles.feedbackContent}>
                <p className={styles.comment}>{feedback.comment}</p>
                <div className={styles.feedbackMeta}>
                  <span className={styles.date}>{new Date(feedback.feedback_date).toLocaleDateString()}</span>
                </div>
              </div>

              <div className={styles.feedbackActions}>
                {currentUserId == feedback.user_id && (
                  <UpdateFeedbackButton onClick={() => handleUpdateClick(feedback)} />
                )}
                {(currentUserId == feedback.user_id || isAdmin) && (
                  <DeleteFeedbackButton onClick={() => handleDeleteClick(feedback)} />
                )}
              </div>
            </div>
          ))
        ) : (
          <div className={styles.noFeedback}>
            <p>No feedback yet. Be the first to share your thoughts!</p>
          </div>
        )}
      </div>

      {showAddModal && (
        <AddFeedbackModal foodId={foodId} onClose={handleModalClose} onSuccess={handleFeedbackSuccess} />
      )}

      {showUpdateModal && selectedFeedback && (
        <UpdateFeedbackModal feedback={selectedFeedback} onClose={handleModalClose} onSuccess={handleFeedbackSuccess} />
      )}

      {showDeleteModal && selectedFeedback && (
        <DeleteFeedbackModal feedback={selectedFeedback} onClose={handleModalClose} onSuccess={handleFeedbackSuccess} />
      )}
    </div>
  )
}
