!function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=0)}([function(e,t,n){e.exports=n(1)},function(e,t,n){"use strict";n.r(t);var r=function(e,t,n,r,o,i,s,a){var l,u="function"==typeof e?e.options:e;if(t&&(u.render=t,u.staticRenderFns=n,u._compiled=!0),r&&(u.functional=!0),i&&(u._scopeId="data-v-"+i),s?(l=function(e){(e=e||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(e=__VUE_SSR_CONTEXT__),o&&o.call(this,e),e&&e._registeredComponents&&e._registeredComponents.add(s)},u._ssrRegister=l):o&&(l=a?function(){o.call(this,(u.functional?this.parent:this).$root.$options.shadowRoot)}:o),l)if(u.functional){u._injectStyles=l;var c=u.render;u.render=function(e,t){return l.call(t),c(e,t)}}else{var f=u.beforeCreate;u.beforeCreate=f?[].concat(f,l):[l]}return{exports:e,options:u}}({mixins:[Fieldtype],inject:["storeName"],data:function(){return{selected:null,showFieldtype:!0,fields:[]}},computed:{form:function(){var e="forms."+this.row+".form.0";return data_get(this.$store.state.publish[this.storeName].values,e)},row:function(){return this.namePrefix.match(/\[(.*?)\]/)[1]}},methods:{updateFields:function(){var e=this;this.$axios.get(cp_url("/mailerlite/form-fields/".concat(this.form))).then((function(t){console.log("response_data",t.data),e.fields=t.data}))}},watch:{form:function(e){var t=this;this.showFieldtype=!1,this.updateFields(),this.$nextTick((function(){return t.showFieldtype=!0}))}},mounted:function(){this.selected=this.value,this.updateFields()}},(function(){var e=this,t=e._self._c;return t("div",{staticClass:"form-field-fieldtype-wrapper"},[e.showFieldtype&&e.form?t("v-select",{attrs:{clearable:!0,options:e.fields,reduce:function(e){return e.id},searchable:!0,placeholder:"Choose a form field..."},on:{input:function(t){return e.$emit("input",t)}},model:{value:e.selected,callback:function(t){e.selected=t},expression:"selected"}}):e._e()],1)}),[],!1,null,null,null).exports;Statamic.booting((function(){Statamic.$components.register("form_fields-fieldtype",r)}))}]);