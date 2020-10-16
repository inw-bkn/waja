<template>
<div class="fixed inset-0 bg-blue-500 flex justify-center items-center">
    <div class="w-full max-w-xs bg-gray-200 p-4 rounded-sm shadow-sm relative">
        <h1 class="text-2xl text-center text-gray-700">PLEASE LOG IN</h1>
        <a
            :href="`${$page.props.app.baseUrl}/auth/google`"
            class="mt-8 cursor-pointer block w-full rounded-sm shadow-sm bg-red-600 text-center text-gray-100 p-2"
        >Google</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/twitter`"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-blue-400 text-center text-gray-100 p-2"
        >Twitter</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/facebook`"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-blue-700 text-center text-gray-100 p-2"
        >Facebook</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/github`"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-gray-800 text-center text-gray-100 p-2"
        >Github</a>
    </div>
</div>
</template>

<script>
export default {
}
</script>
