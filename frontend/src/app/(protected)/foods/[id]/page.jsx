"use client"

import { useState, useEffect } from "react"
import { useParams, useRouter } from "next/navigation"
import { getFoodAction } from "@/actions/foodActions"
import { getFeedbacksAction } from "@/actions/feedbackActions"
import { getUserIdAction } from "@/actions/authActions"
import FoodDetailCard from "@/components/cards/FoodDetailCard"
import FeedbackSection from "@/components/sections/FeedbackSection"
import styles from "./page.module.css"

export default function FoodDetailPage() {
  const params = useParams()
  const router = useRouter()
  const [food, setFood] = useState(null)
  const [feedbacks, setFeedbacks] = useState([])
  const [currentUserId, setCurrentUserId] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  useEffect(() => {
    const fetchData = async () => {
      try {
        const userId = await getUserIdAction()
        setCurrentUserId(userId)

        // Fetch food details
        const foodResponse = await getFoodAction(params.id)
        if (foodResponse.error) {
          setError(foodResponse.error)
        } else {
          setFood(foodResponse.data)
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
      </div>

      <h1 className={styles.title}>Food Details</h1>
      <FoodDetailCard food={food} isAdmin={false} />

      <FeedbackSection
        foodId={params.id}
        feedbacks={feedbacks}
        currentUserId={currentUserId}
        onFeedbackChange={refreshFeedbacks}
      />
    </div>
  )
}
