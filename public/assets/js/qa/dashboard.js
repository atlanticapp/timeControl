document.addEventListener("DOMContentLoaded", function() {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 3000
            };

            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        toastr[data.status](data.message);
                    }
                })
                .catch(console.error);

            const progressBars = document.querySelectorAll('[class*="bg-green-500"], [class*="bg-red-500"]');
            progressBars.forEach(bar => {
                const originalWidth = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = originalWidth;
                }, 300);
            });
        });