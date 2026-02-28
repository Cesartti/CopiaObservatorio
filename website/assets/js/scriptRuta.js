
document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll("button[data-step]");
  const steps = document.querySelectorAll(".step");

  function showStep(stepId) {
    steps.forEach(step => step.classList.remove("active"));
    const active = document.getElementById("step" + stepId);
    if (active) active.classList.add("active");
  }

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const step = btn.getAttribute("data-step");
      showStep(step);
    });
  });

  showStep(1);
})
