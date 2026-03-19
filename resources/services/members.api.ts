import { http } from "./http.config";
import { useAppContextStore } from "../stores/appContext";

export const getTenantMembers = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/members`, { params });
  return response.data;
};

export const getTenantMemberDetail = async <T>(id: number | string): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/members/${id}`);
  return response.data;
};
