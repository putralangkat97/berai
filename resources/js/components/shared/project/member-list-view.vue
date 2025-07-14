<script setup>
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";

const props = defineProps({
  members: Array,
  ownerId: Number,
});
</script>
<template>
  <Table>
    <TableHeader>
      <TableRow>
        <TableHead>Name</TableHead>
        <TableHead>Email</TableHead>
        <TableHead>Role</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow v-for="member in members" :key="member.id">
        <TableCell class="font-medium">{{ member.name }}</TableCell>
        <TableCell class="text-muted-foreground">{{ member.email }}</TableCell>
        <TableCell>
          <Badge v-if="member.id === ownerId">Owner</Badge>
          <Badge v-else variant="outline">Member</Badge>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
