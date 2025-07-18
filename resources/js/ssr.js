import { createInertiaApp } from "@inertiajs/vue3";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
import { createSSRApp, h } from "vue";

createServer(
  (page) =>
    createInertiaApp({
      page,
      render: renderToString,
      resolve: (name) => {
        const pages = import.meta.glob("./pages/**/*.vue", { eager: true });
        return pages[`./pages/${name}.vue`];
      },
      setup({ App, props, plugin }) {
        return createSSRApp({
          render: () => h(App, props),
        }).use(plugin);
      },
    }),
  { cluster: true }
);
