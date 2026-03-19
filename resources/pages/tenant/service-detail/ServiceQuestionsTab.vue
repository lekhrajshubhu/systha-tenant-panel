<template>
    <v-container class="pa-0" fluid>
        <v-row>
            <v-col cols="12" lg="6">
                <div class="border rounded pa-4">
                    <v-card class="pa-0" variant="flat">
                        <v-card-title class="text-h6">Questions</v-card-title>
                        <v-expansion-panels v-if="itemQuestions.length" variant="accordion">
                            <v-expansion-panel v-for="question in itemQuestions" :key="question.key">
                                <v-expansion-panel-title>
                                    <div class="d-flex flex-column">
                                        <span class="text-body-2 font-weight-medium">
                                            {{ question.title }}
                                        </span>
                                        <span class="text-caption text-medium-emphasis">
                                            {{ question.type }} •
                                            {{ question.required ? 'Required' : 'Optional' }} •
                                            {{ question.active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <div>
                                        <v-container>
                                            <v-row>
                                                <v-col cols="12" md="8" lg="6">
                                                    <div>
                                                        <div class="text-caption text-medium-emphasis mb-2">Options
                                                        </div>
                                                        <v-list density="compact" class="pa-0">
                                                            <v-list-item v-for="option in question.options"
                                                                :key="option.id" class="px-0">
                                                                <div
                                                                    class="d-flex justify-space-between w-100 align-center ga-3">
                                                                    <span>{{ option.label }}</span>
                                                                    <v-text-field v-model="option.price" type="number"
                                                                        step="0.01" prefix="$" density="compact"
                                                                        variant="outlined" hide-details
                                                                        class="price-input"
                                                                        @blur="option.price = normalizePrice(option.price)" />
                                                                </div>
                                                            </v-list-item>
                                                            <div v-if="!question.options.length"
                                                                class="text-body-2 text-medium-emphasis">
                                                                No options available.
                                                            </div>
                                                        </v-list>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </div>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <div v-else class="text-body-2 text-medium-emphasis">
                            No item level questions found.
                        </div>
                    </v-card>
                </div>
            </v-col>
            <v-col cols="12" lg="6">
                <div class="border rounded p-4">
                    <ServiceClientFlowTab :service="serviceAny" />
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import ServiceClientFlowTab from './ServiceClientFlowTab.vue';
type QuestionItem = {
    id?: number;
    code?: string;
    title?: string;
    field_type?: string;
    is_required?: boolean;
    is_active?: boolean;
    options?: Array<{
        id: number;
        label: string;
        price_adjustment?: string | number;
    }>;
};

type ServiceDetail = {
    combined_questions?: QuestionItem[];
};

const props = defineProps<{
    service: ServiceDetail;
}>();

// Cast to any to avoid cross-file structural type mismatch when passing to the child component
const serviceAny = props.service as any;

const normalizeQuestions = (items: QuestionItem[] | undefined) => {
    if (!items || !Array.isArray(items)) return [];
    return items.map((item, index) => ({
        key: item.id ?? item.code ?? item.title ?? index,
        title: item.title ?? item.code ?? 'Untitled',
        type: item.field_type ?? '-',
        required: Boolean(item.is_required),
        active: Boolean(item.is_active),
        options:
            item.options?.map((option) => {
                const adjustment =
                    option.price_adjustment !== undefined
                        ? Number(option.price_adjustment) || 0
                        : 0;
                return {
                    id: option.id,
                    label: option.label,
                    price: adjustment.toFixed(2),
                };
            }) ?? [],
    }));
};

const itemQuestions = ref<ReturnType<typeof normalizeQuestions>>([]);

const normalizePrice = (value: string | number) => {
    const numeric = Number(value);
    if (Number.isNaN(numeric)) return '0.00';
    return numeric.toFixed(2);
};

watch(
    () => props.service.combined_questions,
    (next) => {
        itemQuestions.value = normalizeQuestions(next);
    },
    { immediate: true },
);
</script>
<style scoped>
.v-expansion-panel--active>.v-expansion-panel-title {
    background: #ededed !important;
}

.price-input {
    max-width: 120px;
}
</style>
