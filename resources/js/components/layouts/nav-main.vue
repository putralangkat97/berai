<script setup>
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar";
import { usePage } from "@inertiajs/vue3";
import AppLink from "@/components/layouts/app-link.vue";

defineProps({
  items: Array,
});

const page = usePage();
</script>

<template>
  <SidebarGroup class="group-data-[collapsible=icon]:hidden">
    <SidebarGroupLabel>Projects</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in items" :key="item.name">
        <SidebarMenuButton as-child :is-active="page.component.startsWith(item.name)">
          <AppLink :href="item.url">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </AppLink>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
