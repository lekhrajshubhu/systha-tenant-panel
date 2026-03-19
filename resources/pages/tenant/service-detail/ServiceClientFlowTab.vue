<template>
    <v-card class="pa-0" variant="flat">
        <v-card-title class="text-h6">Client Question Flow</v-card-title>
    </v-card>

    <v-divider></v-divider>
    <div>
        <v-container>
            <v-row>
                <v-col cols="12" lg="12">
                    <div>
                        <div>
                            <div
                                v-if="!clientFlowQuestions.length"
                                class="text-body-2 text-medium-emphasis"
                            >
                                No questions available for simulation.
                            </div>
                            <template v-else>
                                <div
                                    class="d-flex align-center justify-space-between mb-4"
                                >
                                    <div class="text-subtitle-2">
                                        Step {{ currentStepIndex + 1 }} of
                                        {{ clientFlowQuestions.length }}
                                    </div>
                                    <v-chip
                                        size="small"
                                        variant="tonal"
                                        color="blue-grey-darken-1"
                                        class="text-uppercase"
                                    >
                                        {{ currentQuestion.source }}
                                    </v-chip>
                                </div>

                                <v-card
                                    v-if="currentQuestion"
                                    variant="flat"
                                    class="pa-4 mb-4"
                                >
                                    <div
                                        class="text-subtitle-1 font-weight-medium mb-2"
                                    >
                                        {{ currentQuestion.title }}
                                    </div>
                                    <div
                                        class="text-caption text-medium-emphasis mb-4"
                                    >
                                        Type:
                                        {{ currentQuestion.field_type }}
                                        <span
                                            v-if="currentQuestion.is_required"
                                            class="ml-2"
                                            >• Required</span
                                        >
                                    </div>

                                    <template
                                        v-if="
                                            currentQuestion.field_type ===
                                                'radio' ||
                                            currentQuestion.field_type ===
                                                'select'
                                        "
                                    >
                                        <v-radio-group
                                            v-model="
                                                answers[currentQuestion.id]
                                            "
                                            :error="showValidationError"
                                            :error-messages="validationMessage"
                                        >
                                            <v-radio
                                                v-for="option in currentQuestion.options ||
                                                []"
                                                :key="option.id"
                                                :label="option.label"
                                                :value="option.id"
                                            />
                                        </v-radio-group>
                                    </template>

                                    <template
                                        v-else-if="
                                            currentQuestion.field_type ===
                                            'checkbox'
                                        "
                                    >
                                        <v-checkbox
                                            v-for="option in currentQuestion.options ||
                                            []"
                                            :key="option.id"
                                            v-model="
                                                answersMulti[currentQuestion.id]
                                            "
                                            :label="option.label"
                                            :value="option.id"
                                            density="compact"
                                        />
                                        <div
                                            v-if="showValidationError"
                                            class="text-caption text-error mt-2"
                                        >
                                            {{ validationMessage }}
                                        </div>
                                    </template>

                                    <template
                                        v-else-if="
                                            currentQuestion.field_type ===
                                            'text'
                                        "
                                    >
                                        <v-text-field
                                            v-model="
                                                answers[currentQuestion.id]
                                            "
                                            label="Enter your answer"
                                            density="compact"
                                            variant="outlined"
                                            :error="showValidationError"
                                            :error-messages="validationMessage"
                                        />
                                    </template>

                                    <template
                                        v-else-if="
                                            currentQuestion.field_type ===
                                            'schedule'
                                        "
                                    >
                                        <v-text-field
                                            v-model="
                                                answers[currentQuestion.id]
                                            "
                                            label="Preferred date/time"
                                            density="compact"
                                            variant="outlined"
                                            placeholder="e.g. 2026-04-01 10:00 AM"
                                            :error="showValidationError"
                                            :error-messages="validationMessage"
                                        />
                                    </template>

                                    <template v-else>
                                        <v-text-field
                                            v-model="
                                                answers[currentQuestion.id]
                                            "
                                            label="Enter your answer"
                                            density="compact"
                                            variant="outlined"
                                            :error="showValidationError"
                                            :error-messages="validationMessage"
                                        />
                                    </template>
                                </v-card>

                                <div
                                    class="d-flex align-center justify-space-between"
                                >
                                    <v-btn
                                        variant="text"
                                        :disabled="currentStepIndex === 0"
                                        @click="goPrevious"
                                    >
                                        Previous
                                    </v-btn>
                                    <v-btn color="primary" @click="goNext">
                                        {{ isLastStep ? "Finish" : "Next" }}
                                    </v-btn>
                                </div>

                                <v-divider v-if="isFinished" class="my-6" />
                            </template>
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" lg="12" v-if="isFinished">
                    <v-card v-if="isFinished" variant="outlined" class="pa-4">
                        <div class="text-subtitle-1 font-weight-medium mb-2">
                            Summary
                        </div>
                        <v-table v-if="summaryRows.length" density="compact">
                            <thead>
                                <tr>
                                    <th class="text-left">Question</th>
                                    <th class="text-left">Answer</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in summaryRows" :key="row.id">
                                    <td>{{ row.title }}</td>
                                    <td class="text-medium-emphasis">
                                        {{ row.answer }}
                                    </td>
                                    <td class="text-right">{{ row.price }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                        <div v-else class="text-body-2 text-medium-emphasis">
                            No answers yet.
                        </div>
                        <v-divider class="my-4" />
                        <div
                            class="d-flex justify-space-between text-subtitle-2"
                        >
                            <span>Total Price Adjustment</span>
                            <span>{{ totalAdjustment }}</span>
                        </div>
                        <div class="mt-4">
                            <v-select
                                v-model="selectedCustomerId"
                                :items="customerOptions"
                                :loading="customersLoading"
                                item-title="name"
                                item-value="id"
                                label="Select customer"
                                density="compact"
                                variant="outlined"
                                clearable
                            />
                            <div class="d-flex justify-end mt-2">
                                <v-btn color="primary" @click="submitSelection">
                                    Submit
                                </v-btn>
                            </div>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { getTenantCustomersAll } from "../../../services/customers.api";
import { createTenantInquiry } from "../../../services/inquiries.api";

type OptionItem = {
    id: number;
    label: string;
    price_adjustment?: string | number;
};

type ClientFlowItem = {
    id: number;
    title: string;
    field_type: string;
    is_required: boolean;
    is_active: boolean;
    sort_order: number;
    next_question_id: number | null;
    source: string;
    // optional code property used in summaries
    code?: string;
    options?: OptionItem[];
};

type ServiceDetail = {
    client_flow?: ClientFlowItem[];
    service_item_id?: number;
};

const props = defineProps<{
    service: ServiceDetail;
}>();

type CustomerOption = {
    id: number;
    name: string;
    avatar?: string | null;
};

const clientFlowQuestions = computed(() => props.service.client_flow ?? []);

const clientFlowRows = computed(() => {
    return clientFlowQuestions.value.map((item) => ({
        id: item.id,
        source: item.source,
        title: item.title,
        type: item.field_type,
        required: item.is_required,
        active: item.is_active,
        sort_order: item.sort_order,
        next: item.next_question_id ?? "-",
        optionsText:
            (item.options || [])
                .map((option) => option.label)
                .filter(Boolean)
                .join(", ") || "-",
    }));
});

const currentStepIndex = ref(0);
const answers = ref<Record<number, number | string | null>>({});
const answersMulti = ref<Record<number, number[]>>({});
const showValidationError = ref(false);
const isFinished = ref(false);
const customersLoading = ref(false);
const customerOptions = ref<CustomerOption[]>([]);
const selectedCustomerId = ref<number | null>(null);

const currentQuestion = computed(() => {
    return clientFlowQuestions.value[currentStepIndex.value];
});

const isLastStep = computed(() => {
    return currentStepIndex.value >= clientFlowQuestions.value.length - 1;
});

const validationMessage = computed(() => {
    return showValidationError.value ? "This question is required." : "";
});

const isAnswered = (question: ClientFlowItem) => {
    if (!question.is_required) return true;
    if (question.field_type === "checkbox") {
        return (answersMulti.value[question.id] || []).length > 0;
    }

    const value = answers.value[question.id];
    return value !== undefined && value !== null && String(value).trim() !== "";
};

const goNext = () => {
    if (!currentQuestion.value) return;
    showValidationError.value = false;
    if (!isAnswered(currentQuestion.value)) {
        showValidationError.value = true;
        return;
    }

    if (isLastStep.value) {
        isFinished.value = true;
        return;
    }

    currentStepIndex.value += 1;
    isFinished.value = false;
};

const goPrevious = () => {
    showValidationError.value = false;
    if (currentStepIndex.value > 0) {
        currentStepIndex.value -= 1;
    }
    isFinished.value = false;
};

const optionById = (question: ClientFlowItem, optionId: number) => {
    return question.options?.find((option) => option.id === optionId);
};

const summaryRows = computed(() => {
    return clientFlowQuestions.value.map((question) => {
        if (question.field_type === "checkbox") {
            const selected = answersMulti.value[question.id] || [];
            const labels = selected
                .map((id) => optionById(question, id)?.label || id)
                .join(", ");
            const price = selected.reduce((acc, id) => {
                const option = optionById(question, id);
                const adjustment =
                    option?.price_adjustment !== undefined
                        ? Number(option.price_adjustment) || 0
                        : 0;
                return acc + adjustment;
            }, 0);
            return {
                id: question.id,
                code: question.code,
                title: question.title,
                answer: labels || "-",
                price: price ? price.toFixed(2) : "0.00",
            };
        }

        const value = answers.value[question.id];
        if (value === undefined || value === null || value === "") {
            return {
                id: question.id,
                code: question.code,
                title: question.title,
                answer: "-",
                price: "0.00",
            };
        }

        const option = optionById(question, Number(value));
        return {
            id: question.id,
            code: question.code,
            title: question.title,
            answer: option?.label || String(value),
            price:
                option?.price_adjustment !== undefined
                    ? (Number(option.price_adjustment) || 0).toFixed(2)
                    : "0.00",
        };
    });
});

const totalAdjustment = computed(() => {
    let total = 0;
    clientFlowQuestions.value.forEach((question) => {
        if (question.field_type === "checkbox") {
            const selected = answersMulti.value[question.id] || [];
            selected.forEach((id) => {
                const option = optionById(question, id);
                if (option?.price_adjustment !== undefined) {
                    total += Number(option.price_adjustment) || 0;
                }
            });
            return;
        }

        const value = answers.value[question.id];
        const option = value ? optionById(question, Number(value)) : null;
        if (option?.price_adjustment !== undefined) {
            total += Number(option.price_adjustment) || 0;
        }
    });

    return total.toFixed(2);
});

watch(currentQuestion, (question) => {
    if (!question) return;
    if (
        question.field_type === "checkbox" &&
        !answersMulti.value[question.id]
    ) {
        answersMulti.value[question.id] = [];
    }
});

watch(clientFlowQuestions, () => {
    currentStepIndex.value = 0;
    showValidationError.value = false;
    isFinished.value = false;
    answers.value = {};
    answersMulti.value = {};
});

const fetchCustomers = async () => {
    customersLoading.value = true;
    try {
        const response = await getTenantCustomersAll<{ data: any[] }>();
        const items = response.data || [];
        customerOptions.value = items.map((item: any) => ({
            id: item.id,
            name: item.name || "Customer",
            avatar: item.avatar ?? null,
        }));
    } catch (error) {
        console.error("Failed to fetch customers:", error);
    } finally {
        customersLoading.value = false;
    }
};

watch(
    isFinished,
    (finished) => {
        if (finished && !customerOptions.value.length) {
            fetchCustomers();
        }
    }
);

const submitSelection = () => {
    const selected = customerOptions.value.find(
        (customer) => customer.id === selectedCustomerId.value
    );

    const payload = {
        service_item_id: props.service.service_item_id,
        customer_id: selected?.id ?? null,
        answers: summaryRows.value,
        raw_answers: {
            single: answers.value,
            multi: answersMulti.value,
        },
        total_adjustment: totalAdjustment.value,
    };

    console.log("Selected customer:", selected);
    console.log("Summary:", summaryRows.value);
    console.log("Total adjustment:", totalAdjustment.value);
    console.log("Payload:", payload);

    createTenantInquiry(payload).catch((error) => {
        console.error("Failed to submit inquiry:", error);
    });
};
</script>
