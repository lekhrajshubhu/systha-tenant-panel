<template>
    <v-container class="py-6 px-14" fluid>
        <v-btn
            variant="text"
            color="primary"
            prepend-icon="mdi-arrow-left"
            class="mb-4"
            :to="{ name: 'tenant.email-templates' }"
        >
            Back to Email Templates
        </v-btn>

        <v-alert v-if="errorMessage" class="mt-4" type="error" variant="tonal">
            {{ errorMessage }}
        </v-alert>

        <v-alert
            v-if="successMessage"
            class="mt-4"
            type="success"
            variant="tonal"
        >
            {{ successMessage }}
        </v-alert>

        <template v-if="template">
            <v-card class="pa-4" variant="flat">
                <div class="d-flex align-center justify-space-between flex-wrap ga-4">
                    <div class="d-flex align-center ga-3">
                        <v-avatar size="44" color="primary" variant="tonal">
                            <v-icon size="22">mdi-email-outline</v-icon>
                        </v-avatar>
                        <div class="d-flex flex-column">
                            <span class="text-h6">{{ template.name || "Email Template" }}</span>
                            <span class="text-caption text-medium-emphasis">
                                ID: {{ template.id }}
                            </span>
                        </div>
                    </div>
                    <v-chip
                        size="small"
                        variant="tonal"
                        label
                        :color="form.active ? 'success' : 'grey-darken-1'"
                    >
                        {{ form.active ? "Active" : "Inactive" }}
                    </v-chip>
                </div>
            </v-card>

            <v-card class="mt-6 px-14 py-6" variant="flat">
                <v-card-text class="pa-4">
                    <v-row dense>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="form.name"
                                label="Template Name"
                                density="comfortable"
                                variant="outlined"
                            />
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="form.subject"
                                label="Email Subject"
                                density="comfortable"
                                variant="outlined"
                            />
                        </v-col>
                        <v-col cols="12">
                            <RichText v-model="form.body" label="Email Body" />
                        </v-col>
                        <v-col cols="12">
                            <v-switch
                                v-model="form.active"
                                color="primary"
                                label="Active"
                                inset
                            />
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider />
                <v-card-actions class="pa-4">
                    <v-btn
                        color="primary"
                        variant="flat"
                        :loading="saving"
                        @click="saveTemplate"
                    >
                        Save Changes
                    </v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import {
    getTenantEmailTemplateDetail,
    updateTenantEmailTemplate,
} from "../../services/emailTemplates.api";
import RichText from "@/components/RichText.vue";

interface EmailTemplateDetail {
    id: number;
    name?: string;
    subject?: string;
    body?: string;
    content?: string;
    template?: string;
    active?: boolean;
}

const route = useRoute();
const templateId = Number(route.params.id);
const template = ref<EmailTemplateDetail | null>(null);
const errorMessage = ref("");
const successMessage = ref("");
const saving = ref(false);

const form = ref({
    name: "",
    subject: "",
    body: "",
    active: false,
});

const hydrateForm = (data: EmailTemplateDetail) => {
    form.value = {
        name: data.name ?? "",
        subject: data.subject ?? "",
        body: data.body ?? data.content ?? data.template ?? "",
        active: data.active ?? false,
    };
};

const loadTemplate = async () => {
    if (!templateId || Number.isNaN(templateId)) {
        errorMessage.value = "Invalid email template ID.";
        return;
    }
    try {
        const response = (await getTenantEmailTemplateDetail(
            templateId
        )) as EmailTemplateDetail;
        template.value = response;
        hydrateForm(response);
    } catch (error: any) {
        errorMessage.value =
            error?.response?.data?.message ||
            "Failed to load email template details.";
    }
};

const saveTemplate = async () => {
    saving.value = true;
    successMessage.value = "";
    errorMessage.value = "";
    try {
        const payload = {
            name: form.value.name,
            subject: form.value.subject,
            body: form.value.body,
            active: form.value.active,
        };
        const response = (await updateTenantEmailTemplate(
            templateId,
            payload
        )) as EmailTemplateDetail;
        template.value = response;
        hydrateForm(response);
        successMessage.value = "Template updated successfully.";
    } catch (error: any) {
        errorMessage.value =
            error?.response?.data?.message ||
            "Failed to update email template.";
    } finally {
        saving.value = false;
    }
};

onMounted(loadTemplate);
</script>
