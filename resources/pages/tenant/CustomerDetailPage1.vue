<template>
  <v-container class="py-6 px-14" fluid>
    <v-btn
      variant="text"
      color="primary"
      prepend-icon="mdi-arrow-left"
      class="mb-4"
      :to="{ name: 'tenant.customers' }"
    >
      Back to Customers
    </v-btn>

    <v-alert v-if="errorMessage" class="mt-4" type="error" variant="tonal">
      {{ errorMessage }}
    </v-alert>

    <template v-else-if="customer">
      <v-card class="pa-0" variant="flat">
        <v-card-title class="text-h5 pa-0 d-flex align-center ga-3">
          <v-avatar size="44" color="primary" variant="tonal">
            <v-icon icon="mdi-account" size="24" />
          </v-avatar>
          <div class="d-flex flex-column">
            <span class="text-h5">{{ customer.name || '-' }}</span>
            <span class="text-body-2 text-medium-emphasis">{{ customer.email }}</span>
          </div>
        </v-card-title>
      </v-card>

      <v-tabs v-model="activeTab" class="mt-6" density="comfortable">
        <v-tab value="overview">Overview</v-tab>
        <v-tab value="appointments">Appointments</v-tab>
        <v-tab value="estimations">Free Estimations</v-tab>
        <v-tab value="reviews">Reviews</v-tab>
        <v-tab value="settings">Settings</v-tab>
      </v-tabs>

      <v-divider />

      <v-tabs-window v-model="activeTab" class="mt-4">
        <v-tabs-window-item value="overview">
          <v-card class="pa-0" variant="flat">
            <v-card-title class="text-h6">Customer Information</v-card-title>
            <v-row class="mt-2" dense>
              <v-col cols="12" md="4"><strong>Email:</strong> {{ customer.email }}</v-col>
              <v-col cols="12" md="4"><strong>Phone:</strong> {{ customer.phone || '-' }}</v-col>
              <v-col cols="12" md="4">
                <strong>Status:</strong>
                <v-chip
                  class="ml-2"
                  size="small"
                  variant="tonal"
                  label
                  :color="customer.status === 'active' ? 'success' : 'grey-darken-1'"
                >
                  {{ customer.status }}
                </v-chip>
              </v-col>
              <v-col cols="12" md="4"><strong>Last Login:</strong> {{ customer.last_login_at || '-' }}</v-col>
            </v-row>
          </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="appointments">
          <v-card class="pa-6" variant="flat">
            <div class="text-body-2 text-medium-emphasis">No appointment data yet.</div>
          </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="estimations">
          <v-card class="pa-6" variant="flat">
            <div class="text-body-2 text-medium-emphasis">No free estimations yet.</div>
          </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="reviews">
          <v-card class="pa-6" variant="flat">
            <div class="text-body-2 text-medium-emphasis">No reviews yet.</div>
          </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="settings">
          <v-card class="pa-6" variant="flat">
            <div class="text-body-2 text-medium-emphasis">Settings are not configured yet.</div>
          </v-card>
        </v-tabs-window-item>
      </v-tabs-window>
    </template>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getTenantCustomerDetail } from '../../services/customers.api'

interface CustomerDetail {
  id: number
  customer_id: number
  name: string | null
  email: string
  phone: string | null
  status: string
  last_login_at: string | null
}

const route = useRoute()
const customerId = route.params.id as string
const customer = ref<CustomerDetail | null>(null)
const errorMessage = ref('')
const activeTab = ref('overview')

onMounted(async () => {
  try {
    const response = await getTenantCustomerDetail<{ data: CustomerDetail }>(customerId)
    customer.value = response.data
  } catch (error: any) {
    errorMessage.value = error?.response?.data?.message || 'Failed to load customer details.'
  }
})
</script>
