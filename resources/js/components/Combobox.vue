<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Option {
    value: string;
    label: string;
}

interface Props {
    modelValue?: string | number;
    options: Option[];
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Select option...',
    searchPlaceholder: 'Search...',
    emptyText: 'No option found.',
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const value = ref(props.modelValue ? String(props.modelValue) : '');

watch(
    () => props.modelValue,
    (newVal) => {
        value.value = newVal ? String(newVal) : '';
    },
);

function handleSelect(selectedValue: string) {
    value.value = selectedValue;
    emit('update:modelValue', selectedValue);
    open.value = false;
}
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                role="combobox"
                :aria-expanded="open"
                class="w-full justify-between font-normal"
                :class="!value && 'text-muted-foreground'"
            >
                <span class="truncate">
                    {{ value ? options.find((option) => option.value === value)?.label : placeholder }}
                </span>
                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-full p-0">
            <Command>
                <CommandInput :placeholder="searchPlaceholder" />
                <CommandEmpty>{{ emptyText }}</CommandEmpty>
                <CommandList>
                    <CommandGroup>
                        <CommandItem
                            v-for="option in options"
                            :key="option.value"
                            :value="option.value"
                            :keywords="[option.label]"
                            @select="handleSelect(option.value)"
                        >
                            <Check class="mr-2 h-4 w-4" :class="value === option.value ? 'opacity-100' : 'opacity-0'" />
                            {{ option.label }}
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
