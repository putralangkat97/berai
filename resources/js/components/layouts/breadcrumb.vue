<script setup>
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from "@/components/ui/breadcrumb";

defineProps({
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <Breadcrumb>
    <BreadcrumbList v-for="item in breadcrumbs" :key="item.label">
      <BreadcrumbItem class="hidden md:block">
        <BreadcrumbLink :href="item.url">{{ item.label }}</BreadcrumbLink>
      </BreadcrumbItem>
      <template v-if="item.subs">
        <BreadcrumbSeparator class="hidden md:block" />
        <BreadcrumbItem v-for="sub in item.subs" :key="sub.label">
          <BreadcrumbPage>{{ sub.label }}</BreadcrumbPage>
        </BreadcrumbItem>
      </template>
    </BreadcrumbList>
  </Breadcrumb>
</template>
