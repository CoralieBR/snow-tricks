import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['picture', 'type', 'secondInput', 'path', 'file'];

    initialize() {
        this.secondInputTargets.forEach(target => {
            target.hidden = true;
        });
    }

    displayFormNextStep(event)
    {
        if (event.target.value == 1) {
            // it's a picture!
            this.fileTarget.hidden = false;
            this.pathTarget.hidden = true;
            this.pathTarget.querySelector('input').value = 'picture-path';
        } else if (event.target.value == 2) {
            // it's a video!
            this.pathTarget.hidden = false;
            this.fileTarget.hidden = true;
            this.fileTarget.querySelector('input').value = null;
            this.pathTarget.querySelector('input').value = null;
        } else {
            this.secondInputTargets.forEach(target => {
                target.hidden = true;
                target.querySelector('input').value = null;
            });
        }
    }

    deletePicture()
    {
        this.pictureTarget.remove();
    }
}