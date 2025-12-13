import { vitePreprocess } from '@sveltejs/vite-plugin-svelte'

export default {
  // Consult https://svelte.dev/docs#compile-time-svelte-preprocess
  // for more information about preprocessors
  preprocess: vitePreprocess(),
  compilerOptions: {
    warningFilter: (warning) => {
      // Ignore state_referenced_locally warnings
      // where we use $state() to initialize from props, then update via effects
      if (warning.code === 'state_referenced_locally') {
        return false;
      }
      return true;
    }
  }
}
