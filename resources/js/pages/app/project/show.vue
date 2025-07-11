<script setup>
import AppLayout from "@/layouts/app.vue";
import { Badge } from "@/components/ui/badge";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Head } from "@inertiajs/vue3";

defineProps({
  project: Object,
});

const breadcrumbs = [
  { label: "Project", url: "/project", subs: [{ label: "Project Details" }] },
];
</script>

<template>
  <Head :title="project.name" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Project Description Card -->
        <Card>
          <CardHeader>
            <CardTitle>Project Details</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-muted-foreground">
              {{
                project.description ||
                "No description was provided for this project."
              }}
            </p>
          </CardContent>
        </Card>

        <!-- Project Members Card -->
        <Card>
          <CardHeader>
            <CardTitle>Project Members</CardTitle>
            <CardDescription>
              Users who have access to this project. The project owner can add
              more members.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Role</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="member in project.members" :key="member.id">
                  <TableCell class="font-medium">{{ member.name }}</TableCell>
                  <TableCell class="text-muted-foreground">{{
                    member.email
                  }}</TableCell>
                  <TableCell>
                    <Badge v-if="member.id === project.owner_id">Owner</Badge>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
