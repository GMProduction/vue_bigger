<template>
  <div style="position: fixed; bottom: 3rem; right: 3rem; z-index: 999 !important">
     <b-alert
      :show="dismissCountDown"
      dismissible
      variant="success"
      @dismissed="dismissCountDown=0"
      @dismiss-count-down="countDownChanged"
    >
    {{message}}
     </b-alert>
  </div>
  
</template>

<script>
// @ is an alias to /src

export default {
  props: ['message'],
   data() {
      return {
        dismissSecs: 5,
        dismissCountDown: 0,
        showDismissibleAlert: true
      }
    },
    methods: {
      countDownChanged(dismissCountDown) {
        this.dismissCountDown = dismissCountDown
      },
      showAlert() {
        this.dismissCountDown = this.dismissSecs
      }
    }
}
</script>
