import { http } from "./http.config";
import { useAppContextStore } from "../stores/appContext";

export const getTenantCustomers = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/customers`, { params });
  return response.data;
};

export const getTenantCustomerDetail = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/customers/${id}`);
  return response.data;
};

export const getTenantCustomersAll = async <T>(): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/customers/all`);
  return response.data;
};
