<script setup>
import {
  AudioWaveform,
  BookOpen,
  Bot,
  Command,
  Frame,
  GalleryVerticalEnd,
  Map,
  PieChart,
  Settings2,
  SquareTerminal,
  LayoutDashboard,
  ClipboardCheck,
} from "lucide-vue-next";
import NavMain from "@/components/layouts/nav-main.vue";
import NavUser from "@/components/layouts/nav-user.vue";
import TeamSwitcher from "@/components/layouts/team-switcher.vue";
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from "@/components/ui/sidebar";
import { router } from "@inertiajs/vue3";

// This is sample data.
const data = {
  teams: [
    {
      name: "Acme Inc",
      logo: GalleryVerticalEnd,
      plan: "Enterprise",
    },
    {
      name: "Acme Corp.",
      logo: AudioWaveform,
      plan: "Startup",
    },
    {
      name: "Evil Corp.",
      logo: Command,
      plan: "Free",
    },
  ],
  items: [
    {
      title: "Dashboard",
      url: "/dashboard",
      icon: LayoutDashboard,
      name: "app/dashboard",
    },
    {
      title: "Project",
      url: "/project",
      icon: Frame,
      name: "app/project",
    },
    {
      title: "Your Task",
      url: "/task",
      icon: ClipboardCheck,
      name: "app/task",
    },
  ],
};

const props = defineProps();
</script>

<template>
  <Sidebar v-bind="props">
    <SidebarHeader>
      <TeamSwitcher :teams="data.teams" />
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="data.items" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
