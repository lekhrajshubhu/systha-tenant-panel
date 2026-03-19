import { http } from "./http.config";

export type TenantLoginPayload = {
  tenantCode: string;
  login_identifier: string;
  password: string;
};

export type TenantLoginResponse = {
  access_token?: string;
  user?: {
    id: number | string;
    name: string;
    email: string;
    role: string;
    tenant: {
      name: string;
      code: string;
    };
  };
  message?: string;
};

export type TenantRefreshResponse = {
  access_token?: string;
  expires_in?: number;
};

export const loginTenant = async (
  payload: TenantLoginPayload,
): Promise<TenantLoginResponse> => {
  const { tenantCode, ...data } = payload;
  const response = await http.post<TenantLoginResponse>(`/api/tenant/${tenantCode}/login/password`, data);
  return response.data;
};

export const refreshTenantToken = async (): Promise<TenantRefreshResponse> => {
  const response = await http.post<TenantRefreshResponse>('/api/tenant/refresh');
  return response.data;
};
