<template>
<div class="fixed inset-0 bg-blue-500 flex justify-center items-center">
    <div class="w-full max-w-xs bg-gray-200 p-4 rounded-sm shadow-sm relative" ref="social">
        <h1 class="text-2xl text-center text-gray-700">PLEASE LOG IN</h1>
        <a
            :href="`${$page.props.app.baseUrl}/auth/google`"
            class="mt-8 cursor-pointer block w-full rounded-sm shadow-sm bg-red-600 text-center text-gray-100 p-2"
        >Log in with Google</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/twitter`"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-blue-400 text-center text-gray-100 p-2"
        >Log in with Twitter</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/facebook`"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-blue-700 text-center text-gray-100 p-2"
        >Log in with Facebook</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/line`"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-green-500 text-center text-gray-100 p-2"
        >Log in with LINE</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/github`"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-gray-800 text-center text-gray-100 p-2"
        >Log in with Github</a>
        <a
            href="https://oauth.telegram.org/auth?bot_id=1351165804&origin=http%3A%2F%2Fwaja.test&embed=1"
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-gray-800 text-center text-gray-100 p-2"
        >Log in with Telegram</a>
        https://oauth.telegram.org/auth?bot_id=1351165804&origin=http%3A%2F%2Fwaja.test&embed=1
        <button
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm flex justify-center items-center"
            style="background-color: #54a9eb;"
        ><div ref="telegram"></div></button>
    </div>
</div>
</template>

<script>
export default {
mounted () {
    //<script async src="https://telegram.org/js/telegram-widget.js?12" data-telegram-login="WAJALogYouInBot" data-size="medium" data-radius="5" data-auth-url="http://waja.test/auth/telegram/callback">
    // create script with given params
    const script = document.createElement('script')
    script.async = true
    script.src = "https://telegram.org/js/telegram-widget.js?12"
    script.setAttribute('data-size', "large")
    // script.setAttribute('data-userpic', this.userpic)
    script.setAttribute("data-userpic", false)
    script.setAttribute('data-telegram-login', "WAJALogYouInBot")
    // script.setAttribute('data-request-access', this.requestAccess)
    script.setAttribute('data-radius', "0")
    // if (this.radius) { script.setAttribute('data-radius', "5") }
    script.setAttribute('data-auth-url', "http://waja.test/auth/telegram/callback")
    // if (this.mode === 'callback') {
    //     window.onTelegramAuth = this.onTelegramAuth
    //     script.setAttribute('data-onauth', 'window.onTelegramAuth(user)')
    // } else {
    //     script.setAttribute('data-auth-url', this.redirectUrl)
    // }
    // window.onTelegramAuth = this.onTelegramAuth
    // script.setAttribute("data-onauth", "window.onTelegramAuth(user)")
    script.setAttribute("data-auth-url", "http://waja.test/auth/telegram/callback")
    
    this.$refs.telegram.appendChild(script)
},
methods: {
    onTelegramAuth (user) {
        // this.$emit('callback', user)
        console.log(user)
    }
},
}
</script>
