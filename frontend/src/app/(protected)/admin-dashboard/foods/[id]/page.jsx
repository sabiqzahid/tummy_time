"use client"

import { useState, useEffect } from "react"
import { useParams, useRouter } from "next/navigation"
import { getFoodAction, deleteFoodAction } from "@/actions/foodActions"
import { getUserIdAction } from "@/actions/authActions"
import { getFeedbacksAction } from "@/actions/feedbackActions"
import { getCategoriesAction } from "@/actions/categoryActions"
import FoodDetailCard from "@/components/cards/FoodDetailCard"
import DeleteGenModal from "@/components/modals/DeleteGenModal"
import UpdateFoodModal from "@/components/modals/UpdateFoodModal"
import FeedbackSection from "@/components/sections/FeedbackSection"
import styles from "./page.module.css"

export default function AdminFoodDetailPage() {
  const params = useParams()
  const router = useRouter()
  const [food, setFood] = useState(null)
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [showUpdateModal, setShowUpdateModal] = useState(false)
  const [feedbacks, setFeedbacks] = useState([])
  const [currentUserId, setCurrentUserId] = useState(null)
  const [deleting, setDeleting] = useState(false)

  useEffect(() => {
    const fetchData = async () => {
      try {
        const userId = await getUserIdAction()
        setCurrentUserId(userId)
        const [foodResponse, categoriesResponse] = await Promise.all([getFoodAction(params.id), getCategoriesAction()])

        if (foodResponse.error) {
          setError(foodResponse.error)
        } else {
          setFood(foodResponse.data)
        }

        if (categoriesResponse.error) {
          console.error("Failed to fetch categories:", categoriesResponse.error)
        } else {
          setCategories(categoriesResponse.data)
        }

        const feedbackResponse = await getFeedbacksAction(params.id)
        if (feedbackResponse.error) {
          console.error("Failed to load feedbacks:", feedbackResponse.error)
        } else {
          setFeedbacks(feedbackResponse.data || [])
        }
      } catch (err) {
        setError("Failed to fetch food details")
      } finally {
        setLoading(false)
      }
    }

    fetchData()
  }, [params.id])

  const handleDelete = async () => {
    setDeleting(true)
    try {
      const response = await deleteFoodAction(params.id)
      if (response.error) {
        setError(response.error)
      } else {
        router.push("/admin-dashboard/foods")
      }
    } catch (err) {
      setError("Failed to delete food")
    } finally {
      setDeleting(false)
      setShowDeleteModal(false)
    }
  }

  const handleUpdateSuccess = (updatedFood) => {
    setFood(updatedFood)
    setShowUpdateModal(false)
  }

  const refreshFeedbacks = async () => {
    try {
      const feedbackResponse = await getFeedbacksAction(params.id)
      if (feedbackResponse.data) {
        setFeedbacks(feedbackResponse.data)
      }
    } catch (err) {
      console.error("Failed to refresh feedbacks:", err)
    }
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading food details...</div>
      </div>
    )
  }

  if (error) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>Error: {error}</div>
      </div>
    )
  }

  if (!food) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>Food not found</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <button onClick={() => router.back()} className={styles.backButton}>
          ← Back
        </button>
        <h1 className={styles.title}>Food Details</h1>
      </div>

      <FoodDetailCard
        food={food}
        onUpdate={() => setShowUpdateModal(true)}
        onDelete={() => setShowDeleteModal(true)}
        isAdmin={true}
      />

      {showDeleteModal && (
        <DeleteGenModal
          isOpen={showDeleteModal}
          onCancel={() => setShowDeleteModal(false)}
          onConfirm={handleDelete}
          title="Delete Food"
          message="Are you sure you want to delete this food item? This action cannot be undone."
          isLoading={deleting}
        />
      )}

      {showUpdateModal && (
        <UpdateFoodModal
          isOpen={showUpdateModal}
          onClose={() => setShowUpdateModal(false)}
          food={food}
          categories={categories}
          onSuccess={handleUpdateSuccess}
        />
      )}

      <FeedbackSection
        foodId={params.id}
        feedbacks={feedbacks}
        currentUserId={currentUserId}
        onFeedbackChange={refreshFeedbacks}
        isAdmin={true}
      />
    </div>
  )
}
