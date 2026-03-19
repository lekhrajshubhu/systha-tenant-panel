<template>
  <v-card class="pa-0" variant="flat">
    <v-card-title class="text-h6">Service Information</v-card-title>
    <v-row class="mt-2" dense>
      <v-col cols="12" md="3"><strong>Category:</strong> {{ service.category_name || '-' }}</v-col>
      <v-col cols="12" md="3"><strong>Group:</strong> {{ service.group_name || '-' }}</v-col>
      <v-col cols="12" md="2"><strong>Base Price:</strong> {{ service.currency }} {{ service.base_price }}</v-col>
      <v-col cols="12" md="2"><strong>Lead Time:</strong> {{ service.lead_time_hours }} hrs</v-col>
    </v-row>
  </v-card>
</template>

<script setup lang="ts">
type ServiceDetail = {
  category_name?: string | null;
  group_name?: string | null;
  base_price?: string | number;
  currency?: string;
  lead_time_hours?: number;
};

defineProps<{
  service: ServiceDetail;
}>();
</script>
