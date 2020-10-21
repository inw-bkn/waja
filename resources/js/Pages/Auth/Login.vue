<template>
<div class="fixed inset-0 flex justify-center items-center bg-pattern">
    <div class="w-full max-w-xs bg-gray-200 p-4 rounded-sm shadow-sm relative">
        <h1 class="text-2xl font-semibold text-center text-gray-700">PLEASE LOG IN</h1>
        <a
            :href="`${$page.props.app.baseUrl}/auth/google`"
            class="flex justify-center items-center mt-8 cursor-pointer block w-full rounded-sm shadow-sm bg-red-600 text-center text-gray-100 p-2"
            style="background-color: #db3236;"
        ><icon-google class="w-5 h-5 mr-2" />Log in with Google</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/twitter`"
            class="flex justify-center items-center mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-blue-400 text-center text-gray-100 p-2"
            style="background-color: #00acee;"
        ><icon-twitter class="w-6 h-6 mr-2" />Log in with Twitter</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/facebook`"
            class="flex justify-center items-center mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-blue-700 text-center text-gray-100 p-2"
            style="background-color: #3b5998;"
        ><icon-facebook class="w-6 h-6 mr-2" />Log in with Facebook</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/line`"
            class="flex justify-center items-center mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-green-500 text-center text-gray-100 p-2"
            style="background-color: #00b900;"
        ><icon-line class="w-6 h-6 mr-2" />Log in with LINE</a>
        <a
            :href="`${$page.props.app.baseUrl}/auth/github`"
            class="flex justify-center items-center mt-4 cursor-pointer block w-full rounded-sm shadow-sm bg-gray-800 text-center text-gray-100 p-2"
            style="background-color: #211f1f;"
        ><icon-github class="w-6 h-6 mr-2" />Log in with Github</a>
        <button
            class="mt-4 cursor-pointer block w-full rounded-sm shadow-sm flex justify-center items-center"
            style="background-color: #54a9eb;"
        ><div ref="telegram"></div></button>
    </div>
</div>
</template>

<script>
import IconFacebook from "@/Components/icons/Facebook"
import IconGithub from "@/Components/icons/Github"
import IconGoogle from "@/Components/icons/Google"
import IconLine from "@/Components/icons/Line"
import IconTwitter from "@/Components/icons/Twitter"
export default {
components: { IconFacebook, IconGithub, IconGoogle, IconLine, IconTwitter },
props: ["configs"],
mounted () {
    const script = document.createElement('script')
    script.async = true
    script.src = "https://telegram.org/js/telegram-widget.js?12"
    script.setAttribute("data-size", "large")
    script.setAttribute("data-userpic", false)
    script.setAttribute("data-userpic", false)
    script.setAttribute("data-telegram-login", this.configs.telegram.client_id)
    script.setAttribute("data-request-access", null)
    script.setAttribute('data-radius', "0")
    script.setAttribute('data-auth-url', this.configs.telegram.redirect)
    this.$refs.telegram.appendChild(script)
},
}
</script>
