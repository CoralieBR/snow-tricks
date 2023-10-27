import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['currentPicture', 'pathInput', 'picture'];

    connect()
    {
        if (this.hasPathInputTarget) {
            this.showCurrentPicture();
        } else {
            this.listenNewPicture();
        }
    }

    showCurrentPicture()
    {
        this.currentPictureTarget.setAttribute('src', 'uploads/media/' + this.pictureTarget.querySelector('input').value);
    }

    listenNewPicture() {
        this.pictureTarget.querySelector('input').addEventListener('click', this.showCurrentPicture());
    }

    deletePicture(event)
    {
        this.pictureTarget.remove();
    }
}