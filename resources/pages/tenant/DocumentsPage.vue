<template>
  <v-container class="py-6 px-14" fluid>
    <v-card class="pa-0" variant="flat">
      <h2 class="pa-0">Documents Templates</h2>
      <v-card-subtitle class="pa-0">Manage your quotation templates for customer communication.</v-card-subtitle>

      <v-divider class="my-6" />

      <v-card flat class="mb-6">
        <v-card-text class="pa-4">
          <v-row align="center">
            <v-col cols="12" sm="6" md="4">
              <v-text-field
                v-model="search"
                density="compact"
                variant="outlined"
                prepend-inner-icon="mdi-magnify"
                label="Search templates"
                clearable
                hide-details
                style="max-width: 360px"
                @update:model-value=""
              />
            </v-col>
            <v-spacer />
            <v-col cols="auto">
              <v-btn
                icon="mdi-refresh"
                variant="text"
                color="medium-emphasis"
                @click=""
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
          @update:options=""
          :hide-default-footer="totalItems <= itemsPerPage"
        >
          <template #[`item.sn`]="{ index }">
            {{ index + 1 }}
          </template>

          <template #[`item.name`]="{ item }">
            <div class="font-weight-medium text-high-emphasis">
              {{ item.name || '-' }}
            </div>
          </template>

          <template #[`item.active`]="{ item }">
            <v-chip
              :color="item.active ? 'success' : 'grey-darken-1'"
              size="small"
              variant="tonal"
              label
              class="px-3 text-uppercase"
            >
              {{ item.active ? 'Active' : 'Inactive' }}
            </v-chip>
          </template>

          <template #[`item.actions`]="{ item }">
            <v-btn color="primary" variant="outlined" size="small">View</v-btn>
          </template>

          <template #no-data>
            <div class="pa-12 text-center">
              <v-icon size="64" color="disabled" class="mb-4">mdi-email-outline</v-icon>
              <div class="text-h6 text-medium-emphasis">No templates found</div>
              <p class="text-body-2 text-disabled">Try adjusting your search.</p>
            </div>
          </template>
        </v-data-table-server>
      </v-card>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { getTenantEmailTemplates } from '../../services/emailTemplates.api'

interface EmailTemplateItem {
  id: number
  name: string
  subject: string
  active: boolean
}

const loading = ref(false)
const search = ref('')
const itemsPerPage = ref(15)
const serverItems = ref<EmailTemplateItem[]>([])
const totalItems = ref(0)
const debounceTimeout = ref<number | null>(null)

const headers = [
  { title: 'SN', key: 'sn', align: 'start' as const, sortable: false },
  { title: 'Name', key: 'name', align: 'start' as const, sortable: true },
  // { title: 'Subject', key: 'subject', align: 'start' as const, sortable: true },
  { title: 'Status', key: 'active', align: 'center' as const, sortable: false },
  { title: 'Created At', key: 'created_at', align: 'center' as const, sortable: true },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
]


</script>
