<script lang="ts">
  import './app.css';
  import Router from 'svelte-spa-router';
  import { push } from 'svelte-spa-router';
  import { wrap } from 'svelte-spa-router/wrap';
  import type { ConditionsFailedEvent } from 'svelte-spa-router';

  import Account from './routes/Account.svelte';
  import Administration from './routes/Administration.svelte';
  import Formats from './routes/Formats.svelte';
  import Home from './routes/Home.svelte';
  import Login from './routes/Login.svelte';
  import Meetings from './routes/Meetings.svelte';
  import ServiceBodies from './routes/ServiceBodies.svelte';
  import Users from './routes/Users.svelte';
  import SpinnerModal from './components/SpinnerModal.svelte';
  import ErrorModal from './components/ErrorModal.svelte';
  import { apiCredentials, authenticatedUser } from './stores/apiCredentials';

  const routes = {
    '/login': Login,
    '/meetings': wrap({
      component: Meetings,
      conditions: [requiresAuthentication]
    }),
    '/formats': wrap({
      component: Formats,
      conditions: [requiresAuthenticationServerAdmin]
    }),
    '/servicebodies': wrap({
      component: ServiceBodies,
      conditions: [requiresAuthenticationAdmin]
    }),
    '/users': wrap({
      component: Users,
      conditions: [requiresAuthenticationAdmin]
    }),
    '/account': wrap({
      component: Account,
      conditions: [requiresAuthentication]
    }),
    '/administration': wrap({
      component: Administration,
      conditions: [requiresAuthentication]
    }),
    '*': wrap({
      component: Home,
      conditions: [requiresAuthentication]
    })
  };

  function requiresAuthentication(): boolean {
    return !!$apiCredentials;
  }

  function requiresAuthenticationAdmin(): boolean {
    if (!requiresAuthentication()) {
      return false;
    }
    // If $authenticatedUser is null, the user data is still being fetched from the API.
    // Allow the route to load temporarily, this guard will be re-evaluated reactively once the user loads.
    // The route components themselves also verify $authenticatedUser before rendering admin-only content.
    if (!$authenticatedUser) {
      return true;
    }
    return $authenticatedUser.type === 'admin' || $authenticatedUser.type === 'serviceBodyAdmin';
  }

  function requiresAuthenticationServerAdmin(): boolean {
    if (!requiresAuthentication()) {
      return false;
    }
    // If $authenticatedUser is null, the user data is still being fetched from the API.
    // Allow the route to load temporarily, this guard will be re-evaluated reactively once the user loads.
    if (!$authenticatedUser) {
      return true;
    }
    return $authenticatedUser.type === 'admin';
  }

  function conditionsFailed(event: ConditionsFailedEvent) {
    if (Object.keys(routes).includes(event.detail.location)) {
      push(`/login?route=${event.detail.location}`);
    } else {
      push('/login');
    }
  }

  $effect(() => {
    if (!$apiCredentials) {
      push('/login');
    }
  });
</script>

<svelte:head>
  <title>BMLT Server</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
</svelte:head>

<Router {routes} restoreScrollState={true} onconditionsFailed={conditionsFailed} />
<SpinnerModal />
<ErrorModal />
