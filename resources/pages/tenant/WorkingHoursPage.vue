<template>
    <v-container class="py-6 px-14" fluid>
    <v-card class="pa-0" variant="flat">
      <v-card-title class="text-h5 pa-0">Working hours</v-card-title>
      <v-card-subtitle class="pa-0">Manage your team members and their access.</v-card-subtitle>
      
      <v-divider class="my-6" />
      
      <v-btn prepend-icon="mdi-account-plus" color="primary" rounded="lg" class="mb-6">Add Staff Member</v-btn>
      
      <v-row>
        <v-col v-for="i in 4" :key="i" cols="12" md="6">
          <v-card border flat class="rounded-xl pa-4 d-flex align-center ga-4">
            <v-avatar color="primary" size="48">
              <v-icon icon="mdi-account-star" />
            </v-avatar>
            <div class="flex-grow-1">
              <div class="text-subtitle-1 font-weight-bold">Staff Member {{ i }}</div>
              <div class="text-caption text-medium-emphasis">Role: {{ i === 1 ? 'Manager' : 'Technician' }}</div>
            </div>
            <v-btn icon="mdi-pencil-outline" size="small" variant="text" />
          </v-card>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>
