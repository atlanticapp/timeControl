Vue.component('Toast', {
    template: `
      <div class="toast" :class="type">
        <div class="toast-header">
          <strong class="me-auto">{{ title }}</strong>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="toast-body">
          <p>{{ message }}</p>
          <slot></slot>
          <button v-if="showConfirmButton" class="btn btn-success" @click="$emit('confirm')">Confirmar</button>
          <button v-if="showCancelButton" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
          <textarea v-if="showTextArea" v-model="note"></textarea>
          <button v-if="showReviewButton" class="btn btn-primary" @click="$emit('review', note)">Enviar</button>
        </div>
      </div>
    `,
    props: {
        title: String,
        message: String,
        type: String,
        showConfirmButton: Boolean,
        showCancelButton: Boolean,
        showTextArea: Boolean,
        showReviewButton: Boolean,
    },
    data() {
        return {
            note: '',
        };
    },
});