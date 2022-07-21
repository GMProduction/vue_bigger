<template>
  <button  :class="['gen-button', bgColor, color]">
      <i v-show="icondepan"  :class="['mdi', icondepan, text == null ? 'mr-0' : 'mr-2']"></i>
      <a v-show="text">{{text}}</a>
      <i v-show="iconbelakang"  :class="['mdi', iconbelakang, 'ml-2']"></i>
      <i v-show="iconOnly"  :class="['mdi', iconOnly]"></i>
  </button>
</template>

<script>
// @ is an alias to /src

export default {
    props: ['text', 'icondepan', 'iconbelakang', 'iconOnly','bg-color', 'color'],
  data() {
      return {
       
    }

  },
}
</script>
