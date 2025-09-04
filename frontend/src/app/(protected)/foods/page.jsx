"use client"

import { useState, useEffect } from "react"
import { useRouter, useSearchParams } from "next/navigation"
import { getCategoriesAction } from "@/actions/categoryActions"
import { getFoodsAction } from "@/actions/foodActions"
import FoodSidebar from "@/components/sidebars/FoodSidebar"
import FoodCard from "@/components/cards/FoodCard"
import Pagination from "@/components/paginations/Pagination"
import styles from "./page.module.css"

export default function FoodsPage() {
  const router = useRouter()
  const searchParams = useSearchParams()

  const [foods, setFoods] = useState([])
  const [categories, setCategories] = useState([])
  const [pagination, setPagination] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [searchTerm, setSearchTerm] = useState(searchParams.get("search") || "")
  const [sidebarOpen, setSidebarOpen] = useState(false)

  const currentFilters = {
    search: searchParams.get("search") || "",
    category: searchParams.get("category") || "",
    is_available: searchParams.get("is_available") || "",
    sort_by_price: searchParams.get("sort_by_price") || "",
    page: searchParams.get("page") || "1",
    per_page: searchParams.get("per_page") || "10",
  }

  useEffect(() => {
    loadCategories()
  }, [])

  useEffect(() => {
    loadFoods()
  }, [searchParams])

  const loadCategories = async () => {
    try {
      const result = await getCategoriesAction()
      if (result.error) {
        setError(result.error)
      } else {
        setCategories(result.data)
      }
    } catch (err) {
      setError("Failed to load categories")
    }
  }

  const loadFoods = async () => {
    setLoading(true)
    try {
      const queryParams = {}

      Object.entries(currentFilters).forEach(([key, value]) => {
        if (value && value !== "") {
          queryParams[key] = value
        }
      })

      const result = await getFoodsAction(queryParams)
      if (result.error) {
        setError(result.error)
      } else {
        setFoods(result.data)
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
    const params = new URLSearchParams(searchParams)

    Object.entries(newFilters).forEach(([key, value]) => {
      if (value && value !== "") {
        params.set(key, value)
      } else {
        params.delete(key)
      }
    })

    if (
      newFilters.search !== undefined ||
      newFilters.category !== undefined ||
      newFilters.is_available !== undefined ||
      newFilters.sort_by_price !== undefined
    ) {
      params.set("page", "1")
    }

    router.push(`/foods?${params.toString()}`)
  }

  const handleSearch = (e) => {
    e.preventDefault()
    updateFilters({ search: searchTerm })
  }

  const handleSearchChange = (e) => {
    setSearchTerm(e.target.value)
  }

  const toggleSidebar = () => {
    setSidebarOpen(!sidebarOpen)
  }

  const handlePageChange = (page) => {
    updateFilters({ page: page.toString() })
  }

  return (
    <div className={styles.container}>
      <div className={styles.content}>
        <FoodSidebar
          categories={categories}
          currentFilters={currentFilters}
          onFilterChange={updateFilters}
          isOpen={sidebarOpen}
          onClose={() => setSidebarOpen(false)}
        />

        <main className={styles.main}>
          <h1 className={styles.title}>Our Food Menu</h1>
          <div className={styles.mainHeader}>
            <button className={styles.sidebarToggle} onClick={toggleSidebar} aria-label="Toggle sidebar">
              ☰
            </button>

            <form onSubmit={handleSearch} className={styles.searchForm}>
              <input
                type="text"
                placeholder="Search foods..."
                value={searchTerm}
                onChange={handleSearchChange}
                className={styles.searchInput}
              />
              <button type="submit" className={styles.searchButton}>
                Search
              </button>
            </form>
          </div>

          {error && <div className={styles.error}>{typeof error === "string" ? error : "An error occurred"}</div>}

          {loading ? (
            <div className={styles.loading}>Loading foods...</div>
          ) : (
            <>
              <div className={styles.foodGrid}>
                {foods.length > 0 ? (
                  foods.map((food) => <FoodCard key={food.id} food={food} />)
                ) : (
                  <div className={styles.noResults}>No foods found</div>
                )}
              </div>

              {pagination && foods.length > 0 && (
                <Pagination
                  currentPage={Number.parseInt(currentFilters.page)}
                  totalPages={pagination.total_pages}
                  onPageChange={handlePageChange}
                />
              )}
            </>
          )}
        </main>
      </div>
    </div>
  )
}
