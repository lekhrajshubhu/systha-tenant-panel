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
            <div>
                <v-row>
                    <v-col cols="12" md="4" lg="4">
                        <div class="mb-4">
                            <ProfileCard :member="member" />
                        </div>

                        <!-- <div class="mb-4">
                            <AddressSection
                                :addresses="demoAddresses"
                            />
                        </div> -->
                        <div class="mb-4">
                            <CompanySection
                                :companies="demoCompanies"
                            />
                        </div>
                        <div class="mb-4">
                            <AssignedServicesSection
                                :services="demoServices"
                            />
                        </div>
                    </v-col>
                    <v-col cols="12" md="8" lg="8">
                        <div>
                            <v-card class="pa-4 elevation-0">
                                <v-tabs
                                    v-model="activeTab"
                                    density="comfortable"
                                >
                                    <v-tab
                                        v-for="tab in tabs"
                                        :key="tab.value"
                                        :value="tab.value"
                                    >
                                        {{ tab.label }}
                                    </v-tab>
                                </v-tabs>

                                <v-divider />

                                <v-tabs-window v-model="activeTab" class="mt-4">
                                    <v-tabs-window-item
                                        v-for="tab in tabs"
                                        :key="tab.value"
                                        :value="tab.value"
                                    >
                                        <v-card
                                            v-if="tab.value === 'overview'"
                                            class="pa-0"
                                            variant="flat"
                                        >
                                            <v-card-title class="text-h6"
                                                >Customer
                                                Information</v-card-title
                                            >
                                            <v-row class="mt-2" dense>
                                                <v-col cols="12" md="4"
                                                    ><strong>Email:</strong>
                                                    {{ member.email }}</v-col
                                                >
                                                <!-- <v-col cols="12" md="4"
                                                    ><strong>Phone:</strong>
                                                    {{
                                                        member.phone || "-"
                                                    }}</v-col
                                                > -->
                                                <v-col cols="12" md="4">
                                                    <strong>Status:</strong>
                                                    <v-chip
                                                        class="ml-2"
                                                        size="small"
                                                        variant="tonal"
                                                        label
                                                        :color="
                                                            member.status ===
                                                            'active'
                                                                ? 'success'
                                                                : 'grey-darken-1'
                                                        "
                                                    >
                                                        {{ member.status }}
                                                    </v-chip>
                                                </v-col>
                                                <v-col cols="12" md="4"
                                                    ><strong
                                                        >Last Login:</strong
                                                    >
                                                    {{
                                                        member.last_login_at ||
                                                        "-"
                                                    }}</v-col
                                                >
                                            </v-row>
                                        </v-card>
                                        <v-card
                                            v-else
                                            class="pa-6"
                                            variant="flat"
                                        >
                                            <div
                                                class="text-body-2 text-medium-emphasis"
                                            >
                                                {{ tab.emptyText }}
                                            </div>
                                        </v-card>
                                    </v-tabs-window-item>
                                </v-tabs-window>
                            </v-card>
                        </div>
                    </v-col>
                </v-row>
            </div>
        </template>
    </v-container>
</template>

<style scoped>
.company-card {
    border: 1px solid rgba(15, 23, 42, 0.08);
    padding: 16px;
    background: #ffffff;
}

.job-chip :deep(.v-chip__content),
.dept-chip :deep(.v-chip__content) {
    letter-spacing: 0.2px;
}
</style>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { getTenantMemberDetail } from '../../services/members.api'

import { useModalStore } from "@/stores/modal";
import AddressSection from "@/components/member/AddressSection.vue";
import CompanySection from "@/components/member/CompanySection.vue";
import AssignedServicesSection from "@/components/member/AssignedServicesSection.vue";
import ProfileCard from "@/components/member/ProfileCard.vue";

interface MemberDetail {
  id: number
  customer_id: number
  name: string | null
  email: string
  phone: string | null
  role: string
  status: string
  last_login_at: string | null
}


const route = useRoute();
const customerId = route.params.id as string;
const member = ref<MemberDetail | null>(null);
const errorMessage = ref("");
const activeTab = ref("appointments");
const modal = useModalStore();
const tabs = [
    {
        value: "appointments",
        label: "Appointments",
        emptyText: "No appointment data yet.",
    },
    // {
    //     value: "estimations",
    //     label: "Inquiries",
    //     emptyText: "No inquiries yet.",
    // },
    // {
    //     value: "reviews",
    //     label: "Payment Cards",
    //     emptyText: "No payment cards yet.",
    // },
    {
        value: "attachments",
        label: "Attachments",
        emptyText: "No attachments yet.",
    },
    { value: "audits", label: "Audits", emptyText: "No audits yet." },
    { value: "notes", label: "Notes", emptyText: "No notes yet." },
];

const demoServices = [
    {
        name: "Termite Control",
        icon: "mdi-home-search-outline",
        color: "#0f766e",
        slug: "termite-control",
        duration: 60,
        base_price: 120,
    },
    {
        name: "Ant Treatment",
        icon: "mdi-bug",
        color: "#0f766e",
        slug: "ant-treatment",
        duration: 45,
        base_price: 90,
    },
    {
        name: "Deep Home Cleaning",
        icon: "mdi-spray-bottle",
        color: "#2563eb",
        slug: "deep-home-cleaning",
        duration: 240,
        base_price: 320,
    },
    {
        name: "Move Out Cleaning",
        icon: "mdi-spray-bottle",
        color: "#2563eb",
        slug: "move-out-cleaning",
        duration: 300,
        base_price: 380,
    },
];

const demoAddresses = [
    {
        label: "Home",
        icon: "mdi-home-outline",
        line1: "221B Market Street",
        line2: "Apt 4B",
        city: "San Francisco",
        state: "CA",
        zip: "94105",
    },
    {
        label: "Office",
        icon: "mdi-office-building-outline",
        line1: "1550 Market St",
        line2: "Suite 200",
        city: "San Francisco",
        state: "CA",
        zip: "94103",
    },
    {
        label: "Billing",
        icon: "mdi-credit-card-outline",
        line1: "500 Terry A Francois Blvd",
        line2: "",
        city: "San Francisco",
        state: "CA",
        zip: "94158",
    },
];
const demoCompanies = [
    {
        name: "Brightline Home Services",
        relation_type: "Owner",
        is_primary: true,
        entity_type: "LLC",
        registration_state: "NY",
        ein: "10-1000001",
        job_title: "Founder",
        department: "Management",
        email: "contact@brightlinehome.com",
        phone: "+1-555-3101",
        website: "https://brightlinehome.com",
        address: {
            line1: "120 Market Street",
            line2: "Suite 400",
            city: "New York",
            state: "NY",
            zip: "10005",
        },
    },
    {
        name: "Summit Facility Care",
        relation_type: "Billing Contact",
        is_primary: false,
        entity_type: "Corporation",
        registration_state: "CA",
        ein: "11-1000002",
        job_title: "Finance Manager",
        department: "Finance",
        email: "hello@summitcare.com",
        phone: "+1-555-3102",
        website: "https://summitcare.com",
        address: {
            line1: "455 Sunset Blvd",
            line2: "Floor 3",
            city: "Los Angeles",
            state: "CA",
            zip: "90028",
        },
    },
    {
        name: "Oakridge Property Experts",
        relation_type: "Authorized Representative",
        is_primary: false,
        entity_type: "LLC",
        registration_state: "TX",
        ein: "12-1000003",
        job_title: "Operations Lead",
        department: "Operations",
        email: "team@oakridgeproperty.com",
        phone: "+1-555-3103",
        website: "https://oakridgeproperty.com",
        address: {
            line1: "900 Congress Ave",
            line2: "Suite 120",
            city: "Austin",
            state: "TX",
            zip: "78701",
        },
    },
];
onMounted(async () => {
    try {
        const response = await getTenantMemberDetail<{
            data: MemberDetail;
        }>(customerId);
        member.value = response.data;
    } catch (error: any) {
        errorMessage.value =
            error?.response?.data?.message ||
            "Failed to load member details.";
    }
});
</script>
