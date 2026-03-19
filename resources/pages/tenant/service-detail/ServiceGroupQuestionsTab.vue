<template>
  <v-card class="pa-0" variant="flat">
    <v-card-title class="text-h6">Group Questions</v-card-title>
    <v-expansion-panels v-if="groupQuestions.length" variant="accordion">
      <v-expansion-panel v-for="question in groupQuestions" :key="question.key">
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
                    <div class="text-caption text-medium-emphasis mb-2">Options</div>
                    <v-list density="compact" class="pa-0">
                      <v-list-item v-for="option in question.options" :key="option.id" class="px-0">
                        <div class="d-flex justify-space-between w-100">
                          <span>{{ option.label }}</span>
                          <span class="text-medium-emphasis">
                            ${{ option.price }}
                          </span>
                        </div>
                      </v-list-item>
                      <div v-if="!question.options.length" class="text-body-2 text-medium-emphasis">
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
      No group questions found.
    </div>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';

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
  group_level_questions?: QuestionItem[];
};

const props = defineProps<{
  service: ServiceDetail;
}>();

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

const groupQuestions = computed(() => normalizeQuestions(props.service.group_level_questions));
</script>
