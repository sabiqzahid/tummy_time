"use server";
import {
  getOrders,
  getNewOrders,
  getOrder,
  createOrder,
  updateOrder,
  deleteOrder
} from "@/libs/api";

// export const actionError = async (response) => {
//   if (typeof response.error === 'object' && response.error !== null) {
//     const errorMessages = {};
    
//     for (const field in response.error) {
//       if (Object.prototype.hasOwnProperty.call(response.error, field)) {
//         if (Array.isArray(response.error[field])) {
//           errorMessages[field] = response.error[field][0];
//         } else {
//           errorMessages[field] = response.error[field];
//         }
//       }
//     }
    
//     return { error: errorMessages };
//   }

//   return { error: { error: response.error } };
// };

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    if (response.error.order_status) {
      errorMessages["order_status"] = response.error.order_status;
    }

    return { error: errorMessages };
  }

  return { error: { error: response.error } };
};

export const getOrdersAction = async (queryParams = {}) => {
  try {
    const response = await getOrders(queryParams);

    if (response.error) {
      return { error: response.error };
    }

    return {
      data: response.data,
      pagination: {
        count: response.total,
        total_pages: Math.ceil(response.total / response.per_page),
        next: response.next_page_url ? new URL(response.next_page_url).search : null,
        previous: response.prev_page_url ? new URL(response.prev_page_url).search : null,
      },
    };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const getNewOrdersAction = async (queryParams = {}) => {
  try {
    const response = await getNewOrders(queryParams);

    if (response.error) {
      return { error: response.error };
    }

    return {
      data: response.data,
      pagination: {
        count: response.total,
        total_pages: Math.ceil(response.total / response.per_page),
        next: response.next_page_url ? new URL(response.next_page_url).search : null,
        previous: response.prev_page_url ? new URL(response.prev_page_url).search : null,
      },
    };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const getOrderAction = async (id) => {
  try {
    const response = await getOrder(id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const createOrderAction = async () => {
  const data = {};

  try {
    const response = await createOrder(data);

    if (response.error) {
      return actionError(response);
    }

    return response;
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const updateOrderAction = async (id, formData) => {
  const order_status = formData.get("order_status");

  const data = {
    order_status,
  };

  try {
    const response = await updateOrder(id, data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};


export const deleteOrderAction = async (id) => {
  try {
    const response = await deleteOrder(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};
