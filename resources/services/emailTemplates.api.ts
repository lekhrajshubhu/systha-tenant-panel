import { http } from "./http.config";
import { useAppContextStore } from "../stores/appContext";

export const getTenantEmailTemplates = async <T>(params: any = {}): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/email-templates`, { params });
  return response.data;
};

export const getTenantEmailTemplateDetail = async <T>(templateId: number): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.get<T>(`/api/tenant/${tenantCode}/email-templates/${templateId}`);
  return response.data;
};

export const updateTenantEmailTemplate = async <T>(
  templateId: number,
  payload: Record<string, unknown>
): Promise<T> => {
  const store = useAppContextStore();
  const tenantCode = store.user?.tenant?.code;

  if (!tenantCode) {
    throw new Error("Tenant code not found in session.");
  }

  const response = await http.put<T>(
    `/api/tenant/${tenantCode}/email-templates/${templateId}`,
    payload
  );
  return response.data;
};
