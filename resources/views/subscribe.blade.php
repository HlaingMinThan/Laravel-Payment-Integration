<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Subscribe
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <form id="payment-form">
            <div id="link-authentication-element">
              <!--Stripe.js injects the Link Authentication Element-->
            </div>
            <div id="payment-element">
              <!--Stripe.js injects the Payment Element-->
            </div>
            <button
              id="paynow"
              class="my-3"
            >
              <div
                class="spinner hidden"
                id="spinner"
              ></div>
              <span id="button-text">Pay now</span>
            </button>
            <div
              id="payment-message"
              class="hidden"
            ></div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('script')
  <script src="https://js.stripe.com/v3/"></script>
  <script>
    // This is your test publishable API key.
const stripe = Stripe("pk_test_51MJL2PIMRpHFeHrggguKY014SKhAIi05s992jWLRrCEqauZvWBBsPQYNZ76LicjDyf4e6UmJ2NajxexDX4rgCSGB00GEy1Brgw");

// The items the customer wants to buy
const items = [{ id: "xl-tshirt" }];

let elements;

initialize();
// checkStatus(); we dont' need this

document
  .querySelector("#payment-form")
  .addEventListener("submit", handleSubmit);

var emailAddress = '';
// Fetches a payment intent and captures the client secret
async function initialize() {

  elements = stripe.elements({ clientSecret:"{{$intent->client_secret}}" });

  const linkAuthenticationElement = elements.create("linkAuthentication");
  linkAuthenticationElement.mount("#link-authentication-element");

  const paymentElementOptions = {
    layout: "tabs",
  };

  const paymentElement = elements.create("payment", paymentElementOptions);
  paymentElement.mount("#payment-element");
}

async function handleSubmit(e) {
  e.preventDefault();
  setLoading(true);

  const { error } = await stripe.confirmPayment({
    elements,
    confirmParams: {
      // Make sure to change this to your payment completion page
      return_url: "http://localhost:4242/public/checkout.html",
      receipt_email: emailAddress,
    },
  });

  // This point will only be reached if there is an immediate error when
  // confirming the payment. Otherwise, your customer will be redirected to
  // your `return_url`. For some payment methods like iDEAL, your customer will
  // be redirected to an intermediate site first to authorize the payment, then
  // redirected to the `return_url`.
  if (error.type === "card_error" || error.type === "validation_error") {
    showMessage(error.message);
  } else {
    showMessage("An unexpected error occurred.");
  }

  setLoading(false);
}

// Fetches the payment intent status after payment submission
async function checkStatus() {
  const clientSecret = new URLSearchParams(window.location.search).get(
    "payment_intent_client_secret"
  );

  if (!clientSecret) {
    return;
  }

  const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

  switch (paymentIntent.status) {
    case "succeeded":
      showMessage("Payment succeeded!");
      break;
    case "processing":
      showMessage("Your payment is processing.");
      break;
    case "requires_payment_method":
      showMessage("Your payment was not successful, please try again.");
      break;
    default:
      showMessage("Something went wrong.");
      break;
  }
}

// ------- UI helpers -------

function showMessage(messageText) {
  const messageContainer = document.querySelector("#payment-message");

  messageContainer.classList.remove("hidden");
  messageContainer.textContent = messageText;

  setTimeout(function () {
    messageContainer.classList.add("hidden");
    messageText.textContent = "";
  }, 4000);
}

// Show a spinner on payment submission
function setLoading(isLoading) {
  if (isLoading) {
    // Disable the button and show a spinner
    document.querySelector("#submit").disabled = true;
    document.querySelector("#spinner").classList.remove("hidden");
    document.querySelector("#button-text").classList.add("hidden");
  } else {
    document.querySelector("#submit").disabled = false;
    document.querySelector("#spinner").classList.add("hidden");
    document.querySelector("#button-text").classList.remove("hidden");
  }
}
  </script>
  @endpush
</x-app-layout>