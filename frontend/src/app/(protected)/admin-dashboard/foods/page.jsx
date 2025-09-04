"use client"

import { useState, useEffect } from "react"
import { useRouter, useSearchParams } from "next/navigation"
import { getCategoriesAction } from "@/actions/categoryActions"
import { getFoodsAction } from "@/actions/foodActions"
import { deleteFoodAction } from "@/actions/foodActions"
import FoodSidebar from "@/components/sidebars/FoodSidebar"
import Pagination from "@/components/paginations/Pagination"
import AdminFoodCard from "@/components/cards/AdminFoodCard"
import DeleteGenModal from "@/components/modals/DeleteGenModal"
import CreateFoodModal from "@/components/modals/CreateFoodModal"
import {CreateFoodButton} from "@/components/buttons/buttons"
import styles from "./page.module.css"

export default function AdminFoodsPage() {
  const router = useRouter()
  const searchParams = useSearchParams()

  const [foods, setFoods] = useState([])
  const [pagination, setPagination] = useState(null)
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const [searchTerm, setSearchTerm] = useState(searchParams.get("search") || "")
  const [showDeleteModal, setShowDeleteModal] = useState(false)
  const [showCreateModal, setShowCreateModal] = useState(false)
  const [foodToDelete, setFoodToDelete] = useState(null)
  const [deleting, setDeleting] = useState(false)

  const [filters, setFilters] = useState({
    search: searchParams.get("search") || "",
    category: searchParams.get("category") || "",
    is_available: searchParams.get("is_available") || "",
    sort_by_price: searchParams.get("sort_by_price") || "",
    page: searchParams.get("page") || "1",
  })

  useEffect(() => {
    loadCategories()
  }, [])

  useEffect(() => {
    loadFoods()
  }, [filters])

  const loadCategories = async () => {
    try {
      const result = await getCategoriesAction()
      if (result.error) {
        console.error("Failed to load categories:", result.error)
      } else {
        setCategories(result.data || [])
      }
    } catch (err) {
      console.error("Failed to load categories:", err)
    }
  }

  const loadFoods = async (currentFilters = filters) => {
    setLoading(true)
    setError(null)

    try {
      const queryParams = new URLSearchParams()

      Object.entries(currentFilters).forEach(([key, value]) => {
        if (value && value !== "") {
          queryParams.append(key, value)
        }
      })

      const result = await getFoodsAction(Object.fromEntries(queryParams))

      if (result.error) {
        setError(result.error)
      } else {
        setFoods(result.data || [])
        setPagination(result.pagination)
        setError(null)
      }
    } catch (err) {
      setError("Failed to load foods")
    } finally {
      setLoading(false)
    }
  }

  const updateFilters = (newFilters) => {
    const updatedFilters = { ...filters, ...newFilters, page: "1" }
    setFilters(updatedFilters)

    const queryParams = new URLSearchParams()
    Object.entries(updatedFilters).forEach(([key, value]) => {
      if (value && value !== "") {
        queryParams.append(key, value)
      }
    })

    const newUrl = `/admin-dashboard/foods?${queryParams.toString()}`
    router.push(newUrl)
  }

  const handlePageChange = (page) => {
    const updatedFilters = { ...filters, page: page.toString() }
    setFilters(updatedFilters)

    const queryParams = new URLSearchParams()
    Object.entries(updatedFilters).forEach(([key, value]) => {
      if (value && value !== "") {
        queryParams.append(key, value)
      }
    })

    const newUrl = `/admin-dashboard/foods?${queryParams.toString()}`
    router.push(newUrl)
  }

  const handleSearch = (e) => {
    e.preventDefault()
    updateFilters({ search: searchTerm })
  }

  const handleDeleteClick = (food) => {
    setFoodToDelete(food)
    setShowDeleteModal(true)
  }

  const handleDeleteConfirm = async () => {
    if (!foodToDelete) return

    setDeleting(true)
    try {
      const result = await deleteFoodAction(foodToDelete.id)

      if (result.error) {
        setError(result.error)
      } else {
        setShowDeleteModal(false)
        setFoodToDelete(null)
        loadFoods()
      }
    } catch (err) {
      setError("Failed to delete food")
    } finally {
      setDeleting(false)
    }
  }

  const handleDeleteCancel = () => {
    setShowDeleteModal(false)
    setFoodToDelete(null)
  }

  const handleCreateSuccess = () => {
    setShowCreateModal(false)
    loadFoods()
  }

  const handleFoodClick = (foodId) => {
    router.push(`/admin-dashboard/foods/${foodId}`)
  }

  const toggleSidebar = () => {
    setSidebarOpen(!sidebarOpen)
  }

  return (
    <div className={styles.container}>
      <FoodSidebar
        categories={categories}
        currentFilters={filters}
        onFilterChange={updateFilters}
        isOpen={sidebarOpen}
        onClose={() => setSidebarOpen(false)}
      />

      <main className={styles.main}>
        <div className={styles.headerTop}>
            <h1 className={styles.title}>Admin - Food Management</h1>
            <CreateFoodButton onClick={() => setShowCreateModal(true)} />
        </div>
        <div className={styles.mainHeader}>
          <button className={styles.sidebarToggle} onClick={toggleSidebar} aria-label="Toggle sidebar">
            ☰
          </button>
          <form onSubmit={handleSearch} className={styles.searchForm}>
            <input
              type="text"
              placeholder="Search foods..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className={styles.searchInput}
            />
            <button type="submit" className={styles.searchButton}>
              Search
            </button>
          </form>
        </div>

        {error && <div className={styles.error}>{typeof error === "object" ? JSON.stringify(error) : error}</div>}

        {loading ? (
          <div className={styles.loading}>Loading foods...</div>
        ) : (
          <>
            <div className={styles.foodGrid}>
              {foods.length > 0 ? (
                foods.map((food) => (
                  <AdminFoodCard
                    key={food.id}
                    food={food}
                    onDelete={() => handleDeleteClick(food)}
                    onClick={() => handleFoodClick(food.id)}
                  />
                ))
              ) : (
                <div className={styles.noResults}>No foods found</div>
              )}
            </div>

            {pagination && pagination.total_pages > 1 && (
              <Pagination
                currentPage={Number.parseInt(filters.page)}
                totalPages={pagination.total_pages}
                onPageChange={handlePageChange}
              />
            )}
          </>
        )}
      </main>

      {showDeleteModal && (
        <DeleteGenModal
          isOpen={showDeleteModal}
          title="Delete Food"
          message={`Are you sure you want to delete "${foodToDelete?.name}"? This action cannot be undone.`}
          onConfirm={handleDeleteConfirm}
          onCancel={handleDeleteCancel}
          loading={deleting}
        />
      )}

      {showCreateModal && (
        <CreateFoodModal
          isOpen={showCreateModal}
          onClose={() => setShowCreateModal(false)}
          categories={categories}
          onSuccess={handleCreateSuccess}
        />
      )}
    </div>
  )
}
