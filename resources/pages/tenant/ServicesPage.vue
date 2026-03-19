<template>
    <v-container class="py-6 px-14" fluid>
    <div class="d-flex align-center justify-space-between mb-8">
      <div>
        <h1 class="text-h4 font-weight-black text-high-emphasis mb-1">Services</h1>
        <p class="text-body-1 text-medium-emphasis">Manage your service offerings, pricing, and visibility.</p>
      </div>
      <v-btn color="primary" prepend-icon="mdi-plus" rounded size="large" elevation="2">
        Add Service
      </v-btn>
    </div>

    <v-card flat class="mb-6">
      <v-card-text class="pa-4">
        <v-row align="center">
          <v-col cols="12" sm="6" md="4">
            <v-text-field
              v-model="search"
              density="compact"
              variant="outlined"
              prepend-inner-icon="mdi-magnify"
              label="Search services"
              clearable
              hide-details
              style="max-width: 360px"
              @update:model-value="onSearch"
            />
          </v-col>
          <v-spacer />
         
          <v-col cols="auto">
            <v-btn
              icon="mdi-refresh"
              variant="text"
              color="medium-emphasis"
              @click="fetchItems"
            />
          </v-col>
        </v-row>
      </v-card-text>

      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :items="serverItems"
        :items-length="totalItems"
        :loading="loading"
        :search="search"
        class=""
        @update:options="loadItems"
        :hide-default-footer="totalItems <= itemsPerPage"
      >
        <template #[`item.service_name`]="{ item }">
          <div class="d-flex align-center py-2">
            <v-avatar :color="item.meta?.color || 'primary'" variant="tonal" rounded size="30" class="mr-3">
              <v-icon size="18">{{ item.meta?.icon_md || 'mdi-hammer-wrench' }}</v-icon>
            </v-avatar>
            <div class="font-weight-medium text-high-emphasis">
              {{ item.service_name }}
            </div>
          </div>
        </template>

        <template #[`item.base_price`]="{ item }">
          <div class="font-weight-medium">
            {{ item.currency }} {{ item.base_price }}
          </div>
        </template>

        <template #[`item.is_active`]="{ item }">
          <v-chip
            :color="item.is_active ? 'success' : 'error'"
            size="small"
            variant="tonal"
            label
            class="px-3"
          >
            {{ item.is_active ? 'Active' : 'Inactive' }}
          </v-chip>
        </template>

        <template #[`item.actions`]="{ item }">
          <v-btn  variant="tonal" size="small" color="info" class="mr-1" :to="{ name: 'tenant.service-detail', params: { id: item.id } }" >Details</v-btn>
        </template>

        <template #no-data>
          <div class="pa-12 text-center">
            <v-icon size="64" color="disabled" class="mb-4">mdi-package-variant</v-icon>
            <div class="text-h6 text-medium-emphasis">No services found</div>
            <p class="text-body-2 text-disabled">Try adjusting your search or add a new service.</p>
          </div>
        </template>
      </v-data-table-server>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { getTenantServices } from '../../services/tenantServices.api'

interface ServiceItem {
  id: number;
  service_name: string;
  category_name: string;
  group_name: string;
  is_active: boolean;
  base_price: string | number;
  currency: string;
  lead_time_hours: number;
  updated_at: string;
  meta: {
    color?: string;
    icon_md?: string;
  };
}

const loading = ref(false)
const search = ref('')
const itemsPerPage = ref(15)
const serverItems = ref<ServiceItem[]>([])
const totalItems = ref(0)
const debounceTimeout = ref<number | null>(null)

const headers = [
  { title: 'Service', key: 'service_name', align: 'start' as const, sortable: true },
  { title: 'Category', key: 'category_name', align: 'start' as const, sortable: true },
  { title: 'Base Price', key: 'base_price', align: 'start' as const, sortable: true },
  { title: 'Lead Time (Hrs)', key: 'lead_time_hours', align: 'center' as const, sortable: true },
  { title: 'Status', key: 'is_active', align: 'center' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
]

const loadItems = async ({ page, itemsPerPage, sortBy }: any) => {
  loading.value = true
  try {
    const params: any = {
      page,
      per_page: itemsPerPage,
      search: search.value,
    }

    if (sortBy.length) {
      params.sort_by = sortBy[0].key
      params.sort_order = sortBy[0].order
    }

    const response: any = await getTenantServices(params)
    serverItems.value = response.data
    totalItems.value = response.meta.total
  } catch (error) {
    console.error('Failed to fetch services:', error)
  } finally {
    loading.value = false
  }
}

const fetchItems = () => {
  loadItems({ page: 1, itemsPerPage: itemsPerPage.value, sortBy: [] })
}

const onSearch = () => {
  if (debounceTimeout.value) clearTimeout(debounceTimeout.value)
  debounceTimeout.value = window.setTimeout(() => {
    fetchItems()
  }, 500)
}
</script>

<style scoped>

</style>
