<template>
  <v-container class="py-6 px-14" fluid>
    <v-btn
      variant="text"
      color="primary"
      prepend-icon="mdi-arrow-left"
      class="mb-4"
      :to="{ name: 'tenant.staffs' }"
    >
      Back to Staffs
    </v-btn>

    <v-alert v-if="errorMessage" class="mt-4" type="error" variant="tonal">
      {{ errorMessage }}
    </v-alert>

    <template v-else-if="member">
      <v-card class="pa-0" variant="flat">
        <v-card-title class="text-h5 pa-0 d-flex align-center ga-3">
          <v-avatar size="44" color="primary" variant="tonal">
            <v-icon icon="mdi-account" size="24" />
          </v-avatar>
          <div class="d-flex flex-column">
            <span class="text-h5">{{ member.name || '-' }}</span>
            <span class="text-body-2 text-medium-emphasis">{{ member.email }}</span>
          </div>
        </v-card-title>
      </v-card>

      <v-tabs v-model="activeTab" class="mt-6" density="comfortable">
        <v-tab value="overview">Overview</v-tab>
        <v-tab value="settings">Settings</v-tab>
        <v-tab value="activity">Activity</v-tab>
      </v-tabs>

      <v-divider />

      <v-tabs-window v-model="activeTab" class="mt-4">
        <v-tabs-window-item value="overview">
          <v-card class="pa-0" variant="flat">
            <v-card-title class="text-h6">Staff Information</v-card-title>
            <v-row class="mt-2" dense>
              <v-col cols="12" md="4"><strong>Email:</strong> {{ member.email }}</v-col>
              <v-col cols="12" md="4">
                <strong>Role:</strong>
                <v-chip class="ml-2" size="small" variant="tonal" label>
                  {{ member.role }}
                </v-chip>
              </v-col>
              <v-col cols="12" md="4">
                <strong>Status:</strong>
                <v-chip
                  class="ml-2"
                  size="small"
                  variant="tonal"
                  label
                  :color="member.status === 'active' ? 'success' : 'grey-darken-1'"
                >
                  {{ member.status }}
                </v-chip>
              </v-col>
              <v-col cols="12" md="4"><strong>Last Login:</strong> {{ member.last_login_at || '-' }}</v-col>
            </v-row>
          </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="settings">
          <v-card class="pa-6" variant="flat">
            <div class="text-body-2 text-medium-emphasis">Settings are not configured yet.</div>
          </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="activity">
          <v-card class="pa-6" variant="flat">
            <div class="text-body-2 text-medium-emphasis">No activity recorded yet.</div>
          </v-card>
        </v-tabs-window-item>
      </v-tabs-window>
    </template>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getTenantMemberDetail } from '../../services/members.api'

interface MemberDetail {
  id: number
  name: string | null
  email: string
  role: string
  status: string
  last_login_at: string | null
}

const route = useRoute()
const memberId = route.params.id as string
const member = ref<MemberDetail | null>(null)
const errorMessage = ref('')
const activeTab = ref('overview')

onMounted(async () => {
  try {
    const response = await getTenantMemberDetail<{ data: MemberDetail }>(memberId)
    member.value = response.data
  } catch (error: any) {
    errorMessage.value = error?.response?.data?.message || 'Failed to load staff details.'
  }
})
</script>
