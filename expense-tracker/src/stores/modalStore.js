import { defineStore } from 'pinia';

export const useModalStore = defineStore('modal', {
  state: () => ({
    isModalVisible: false,
    deleteTarget: null
  }),

  actions: {
    showDeleteConfirmation(target) {
      this.deleteTarget = target;
      this.isModalVisible = true;
    },

    hideModal() {
      this.isModalVisible = false;
      this.deleteTarget = null;
    }
  }
});
