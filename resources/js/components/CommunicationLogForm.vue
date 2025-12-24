<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/CommunicationLogController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { useForm } from '@inertiajs/vue3';
import { MessageSquarePlus } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    alumnusId: number;
}>();

const form = useForm({
    type: 'call',
    outcome: 'successful',
    notes: '',
    occurred_at: new Date().toISOString().slice(0, 16), // Current datetime-local format
});

const emits = defineEmits<{
    (e: 'success'): void;
}>();

function submit() {
    form.post(store(props.alumnusId).url, {
        onSuccess: () => {
            form.reset('notes');
            form.type = 'call';
            form.outcome = 'successful';
            form.occurred_at = new Date().toISOString().slice(0, 16);
            toast.success('Communication logged successfully');
            emits('success');
        },
    });
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-lg flex items-center gap-2">
                <MessageSquarePlus class="h-5 w-5" />
                Log Interaction
            </CardTitle>
        </CardHeader>
        <CardContent>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label>Type</Label>
                        <Select v-model="form.type">
                            <SelectTrigger>
                                <SelectValue placeholder="Select type" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="call">Phone Call</SelectItem>
                                <SelectItem value="sms">SMS</SelectItem>
                                <SelectItem value="email">Email</SelectItem>
                                <SelectItem value="whatsapp">WhatsApp</SelectItem>
                                <SelectItem value="visit">Physical Visit</SelectItem>
                                <SelectItem value="other">Other</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <Label>Outcome</Label>
                        <Select v-model="form.outcome">
                            <SelectTrigger>
                                <SelectValue placeholder="Select outcome" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="pending">Pending</SelectItem>
                                <SelectItem value="successful">Successful</SelectItem>
                                <SelectItem value="no_answer">No Answer</SelectItem>
                                <SelectItem value="busy">Busy / Line Engaged</SelectItem>
                                <SelectItem value="wrong_number">Wrong Number</SelectItem>
                                <SelectItem value="voicemail">Left Voicemail</SelectItem>
                                <SelectItem value="scheduled_callback">Scheduled Callback</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label>Date & Time</Label>
                    <Input type="datetime-local" v-model="form.occurred_at" required />
                </div>

                <div class="space-y-2">
                    <Label>Notes</Label>
                    <Textarea v-model="form.notes" placeholder="Details about the conversation..." rows="3" />
                </div>

                <Button type="submit" :disabled="form.processing" class="w-full">
                    {{ form.processing ? 'Saving...' : 'Log Interaction' }}
                </Button>
            </form>
        </CardContent>
    </Card>
</template>
