<script setup>
import { formatDistanceToNow } from "date-fns";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";

const props = defineProps({
  activities: Array,
  taskStatuses: Array,
  taskPriorities: Array,
});
</script>

<template>
  <ul class="space-y-4">
    <li v-for="activity in activities" :key="activity.id" class="flex items-start space-x-3">
      <Avatar class="h-8 w-8">
        <AvatarImage :src="activity.causer?.avatar ?? 'https://github.com/unovue.png'" />
        <AvatarFallback>
          {{ activity.causer?.name.charAt(0) ?? "?" }}
        </AvatarFallback>
      </Avatar>
      <div class="flex-grow">
        <p class="text-sm">
          <span class="font-semibold">{{ activity.causer?.name ?? "A user" }}</span>
          {{ activity.description }}
          <span v-if="activity.subject" class="font-semibold">
            "{{ activity.subject.title }}"
          </span>
        </p>
        <div
          v-if="activity.properties.old"
          class="text-xs text-muted-foreground mt-1 pl-4 border-l-2"
        >
          <p v-if="activity.properties.old.status">
            Status changed from
            <span class="font-medium">
              {{
                taskStatuses.find((s) => s.value === activity.properties.old.status)?.label ??
                "Unknown"
              }}
            </span>
            <template v-if="activity.properties.attributes?.status">
              to
              <span class="font-medium">
                {{
                  taskStatuses.find((s) => s.value === activity.properties.attributes.status)?.label
                }}
              </span>
            </template>
          </p>
          <p v-if="activity.properties.old.priority">
            Priority changed from
            <span class="font-medium">
              {{
                taskPriorities.find((p) => p.value === activity.properties.old.priority)?.label ??
                "Unknown"
              }}
            </span>
            <template v-if="activity.properties.attributes?.priority">
              to
              <span class="font-medium">
                {{
                  taskPriorities.find((p) => p.value === activity.properties.attributes.priority)
                    ?.label
                }}
              </span>
            </template>
          </p>
        </div>
        <p class="text-xs text-muted-foreground mt-1">
          {{ formatDistanceToNow(new Date(activity.created_at), { addSuffix: true }) }}
        </p>
      </div>
    </li>
    <li
      v-if="!activities || activities.length === 0"
      class="text-center text-muted-foreground py-8"
    >
      No activity recorded yet.
    </li>
  </ul>
</template>
