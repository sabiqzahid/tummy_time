import { ApiClient } from "./apiClient";

const API_URL = process.env.API_BASE_URL;
const apiClient = new ApiClient(API_URL);

// API functions
export const login = async (data) => {
  return apiClient.post("/login/", data);
};

export const logout = async () => {
  return await apiClient.post("/logout/");
};

export const getUsers = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/users/?${params.toString()}`);
};

export const getUser = async (id) => {
  return apiClient.get(`/users/${id}/`);
};

export const createUser = async (data) => {
  return apiClient.post("/users/", data);
};

export const createStaffUser = async (data) => {
  return apiClient.post("/users/staff/", data);
};

export const updateUser = async (id, data) => {
  return apiClient.patch(`/users/${id}/`, data);
};

export const deleteUser = async (id) => {
  return apiClient.delete(`/users/${id}/`);
};

export const getCategories = async () => {
  return apiClient.get(`/categories/`);
};

export const getCategory = async (id) => {
  return apiClient.get(`/categories/${id}/`);
};

export const createCategory = async (data) => {
  return apiClient.post("/categories/", data);
};

export const deleteCategory = async (id) => {
  return apiClient.delete(`/categories/${id}/`);
};

export const getFoods = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/foods/?${params.toString()}`);
};

export const getFood = async (id) => {
  return apiClient.get(`/foods/${id}/`);
};

export const getMostSoldFoods = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/foods/most-sold?${params.toString()}`);
};

export const createFood = async (data) => {
  return apiClient.post("/foods/", data, {}, true);
};

export const updateFood = async (id, data, isImage = false) => {
  if (isImage) {
    data.append('_method', 'PATCH');
    return apiClient.post(`/foods/${id}/`, data, {}, true);
  }
  return apiClient.patch(`/foods/${id}/`, data);
};

export const deleteFood = async (id) => {
  return apiClient.delete(`/foods/${id}/`);
};

export const getCartItems = async (user_id) => {
  return apiClient.get(`/cart-items/${user_id}/`);
};

export const updateCartItems = async (cart_id, data) => {
  return apiClient.put(`/cart-items/${cart_id}/`, data);
};

export const deleteCartItem = async (cart_id) => {
  return apiClient.delete(`/cart-items/${cart_id}/`);
};

export const getOrders = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/orders/?${params.toString()}`);
};

export const getOrder = async (id) => {
  return apiClient.get(`/orders/${id}/`);
};

export const getNewOrders = async (queryParams = {}) => {
  const params = new URLSearchParams(queryParams);
  return apiClient.get(`/orders/new/?${params.toString()}`);
}

export const createOrder = async (data) => {
  return apiClient.post("/orders/", data);
};

export const updateOrder = async (id, data) => {
  return apiClient.patch(`/orders/${id}/`, data);
};

export const deleteOrder = async (id) => {
  return apiClient.delete(`/orders/${id}/`);
};

export const getPayments = async () => {
  return apiClient.get(`/payments/`);
};

export const getPayment = async (id) => {
  return apiClient.get(`/payments/${id}/`);
};

export const createPayment = async (data) => {
  return apiClient.post("/payments/", data);
};

export const deletePayment = async (id) => {
  return apiClient.delete(`/payments/${id}/`);
};

export const getFeedbacks = async (food_id) => {
  return apiClient.get(`/feedbacks/${food_id}/`);
};

export const createFeedback = async (data) => {
  return apiClient.post("/feedbacks/", data);
};

export const updateFeedback = async (id, data) => {
  return apiClient.patch(`/feedbacks/${id}/`, data);
};

export const deleteFeedback = async (id) => {
  return apiClient.delete(`/feedbacks/${id}/`);
};