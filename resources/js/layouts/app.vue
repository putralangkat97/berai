<script>
export const description = "A sidebar that collapses to icons.";
export const iframeHeight = "800px";
export const containerClass = "w-full h-full";
</script>

<script setup>
import AppSidebar from "@/components/layouts/app-sidebar.vue";
import { Separator } from "@/components/ui/separator";
import { SidebarInset, SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import Breadcrumb from "@/components/layouts/breadcrumb.vue";

defineProps({
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset>
      <header
        class="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12"
      >
        <div class="flex items-center gap-2 px-4">
          <SidebarTrigger class="-ml-1" />
          <Separator orientation="vertical" class="mr-2 h-4" />
          <Breadcrumb :breadcrumbs="breadcrumbs" />
        </div>
      </header>
      <div class="mx-6 mb-6">
        <slot />
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>
