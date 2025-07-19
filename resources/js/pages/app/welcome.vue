<script setup>
import { Head } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import AppLayout from "@/layouts/app.vue";
import AppLink from "@/components/layouts/app-link.vue";

import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";

const breadcrumbs = [{ label: "Welcome" }];
</script>

<template>
  <Head title="Welcome!" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-12">
      <Card class="text-center">
        <CardHeader>
          <CardTitle>Welcome to Berai!</CardTitle>
          <CardDescription>
            We're excited to have you on board. The first step to getting organized is to create
            your first project.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Button size="lg" as-child class="mt-1">
            <AppLink href="/project/create"> Create Your First Project </AppLink>
          </Button>
          <div class="mt-2">
            <AppLink
              href="/dashboard"
              class="text-sm text-muted-foreground hover:text-primary transition-colors"
            >
              I'll do this later, take me to my dashboard
            </AppLink>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
