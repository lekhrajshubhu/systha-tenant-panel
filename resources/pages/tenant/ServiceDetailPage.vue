<template>
    <v-container class="py-6 px-14" fluid>
    <v-btn
      variant="text"
      color="primary"
      prepend-icon="mdi-arrow-left"
      class="mb-4"
      :to="{ name: 'tenant.services' }"
    >
      Back to Services
    </v-btn>

    <v-alert v-if="errorMessage" class="mt-4" type="error" variant="tonal">
      {{ errorMessage }}
    </v-alert>

    <template v-else-if="service">
      <v-card variant="flat" class="pa-4">
        <v-card-title class="d-flex align-center justify-space-between flex-wrap ga-4 pa-0">
          <div class="d-flex align-center ga-3">
            <v-avatar size="48" :color="service.meta?.color || 'primary'" variant="tonal">
              <v-icon :icon="service.meta?.icon_md || 'mdi-briefcase-outline'" size="26" />
            </v-avatar>
            <div class="d-flex flex-column">
              <span class="text-h5">{{ service.name }}</span>
              <span class="text-caption text-medium-emphasis">{{ serviceSlug }}</span>
            </div>
          </div>
          <v-chip size="small" variant="tonal" label :color="service.is_active ? 'success' : 'grey-darken-1'">
            {{ service.is_active ? 'Active' : 'Inactive' }}
          </v-chip>
        </v-card-title>
        <v-card-text style="min-height: 500px;">
          <v-tabs v-model="activeTab" class="mt-6" color="primary" variant="text">
            <v-tab v-for="tab in tabs" :key="tab.value" :value="tab.value">
              {{ tab.label }}
            </v-tab>
          </v-tabs>
          <v-divider></v-divider>
    
          <div class="mt-4">
            <component :is="activeTabComponent" :service="service" />
          </div>
        </v-card-text>
       
    
      </v-card>
    </template>

  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { getTenantServiceDetail } from '../../services/tenantServices.api';
import ServiceOverviewTab from './service-detail/ServiceOverviewTab.vue';
import ServiceGroupQuestionsTab from './service-detail/ServiceGroupQuestionsTab.vue';
import ServiceItemQuestionsTab from './service-detail/ServiceItemQuestionsTab.vue';
import ServiceClientFlowTab from './service-detail/ServiceClientFlowTab.vue';
import ServiceAppointmentsTab from './service-detail/ServiceAppointmentsTab.vue';
import ServiceFreeEstimationTab from './service-detail/ServiceFreeEstimationTab.vue';
import ServiceQuestionsTab from './service-detail/ServiceQuestionsTab.vue';
import ServiceInspectionTab from './service-detail/ServiceInspectionTab.vue';
import ServiceSubscriptionsTab from './service-detail/ServiceSubscriptionTab.vue';

interface ServiceDetail {
  id: number;
  name: string;
  meta: { icon_md?: string; color?: string } | null;
  category_name: string | null;
  group_name: string | null;
  is_active: boolean;
  base_price: string | number;
  currency: string;
  lead_time_hours: number;
  questions?: any[];
  direct_questions?: any[];
  group_level_questions?: Array<{
    id: number;
    title: string;
    field_type: string;
    is_required: boolean;
    is_active: boolean;
    sort_order: number;
    next_question_id: number | null;
    source: string;
    options?: Array<{
      id: number;
      label: string;
      price_adjustment: string | number;
    }>;
  }>;
  service_item_level_questions?: Array<{
    id: number;
    title: string;
    field_type: string;
    is_required: boolean;
    is_active: boolean;
    sort_order: number;
    next_question_id: number | null;
    source: string;
    options?: Array<{
      id: number;
      label: string;
      price_adjustment: string | number;
    }>;
  }>;
  combined_questions?: Array<{
    id: number;
    title: string;
    field_type: string;
    is_required: boolean;
    is_active: boolean;
    sort_order: number;
    next_question_id: number | null;
    source: string;
    options?: Array<{
      id: number;
      label: string;
      price_adjustment: string | number;
    }>;
  }>;
  client_flow?: Array<{
    id: number;
    title: string;
    field_type: string;
    is_required: boolean;
    is_active: boolean;
    sort_order: number;
    next_question_id: number | null;
    source: string;
    options?: Array<{
      id: number;
      label: string;
      price_adjustment: string | number;
    }>;
  }>;
}

const route = useRoute();
const serviceId = Number(route.params.id);
const service = ref<ServiceDetail | null>(null);
const errorMessage = ref('');
const activeTab = ref('appointments');
const tabs = [
  { value: 'appointments', label: 'Appointments', component: ServiceAppointmentsTab },
  { value: 'inquiries', label: 'Free Estimation', component: ServiceFreeEstimationTab },
  { value: 'inspections', label: 'Inspections', component: ServiceInspectionTab },
  { value: 'subscriptions', label: 'Subscriptions', component: ServiceSubscriptionsTab },
  { value: 'questions', label: 'Questions', component: ServiceQuestionsTab },
];

const serviceSlug = computed(() => {
  if (!service.value?.name) return '';
  return service.value.name
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
});

const activeTabComponent = computed(() => {
  return tabs.find((tab) => tab.value === activeTab.value)?.component ?? ServiceOverviewTab;
});

onMounted(async () => {
  try {
    const response = await getTenantServiceDetail(serviceId) as ServiceDetail;
    service.value = response;
  } catch (error: any) {
    errorMessage.value = error?.response?.data?.message || 'Failed to load service details.';
  }
});
</script>

<style scoped>
.v-expansion-panel--active>.v-expansion-panel-title {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    background: #ededed;
}
</style>
