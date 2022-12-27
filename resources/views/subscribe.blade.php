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
          <form
            id="payment-form"
            action="/subscribe"
            method="POST"
          >
            @csrf

            <div>
              Normal <input
                type="radio"
                name="plan"
                value="price_1MJiDsIMRpHFeHrg0d5POxcE"
                checked
              >
              Premium <input
                type="radio"
                name="plan"
                value="price_1MJiDsIMRpHFeHrg00Gx1cYZ"
              >
            </div>
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


let elements;

initialize();
// checkStatus(); we dont' need this

document
  .querySelector("#payment-form")
  .addEventListener("submit", handleSubmit);


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

  const { setupIntent,error } = await stripe.confirmSetup({
    elements,
    confirmParams: {
      // Make sure to change this to your payment completion page
      return_url: "http://localhost:4242/public/checkout.html",
    },
    redirect:'if_required'
  });
  if(error){
    if (error.type === "card_error" || error.type === "validation_error") {
    showMessage(error.message);
  } else {
    showMessage("An unexpected error occurred.");
  }
  }else{
    //append a new hidden input element with value to the form and submit

    let form = document.getElementById('payment-form');
    let input = document.createElement('input');
    input.setAttribute('type','hidden');
    input.setAttribute('name','paymentMethod');
    input.setAttribute('value',setupIntent.payment_method);
    form.appendChild(input);
    form.submit();//call server side submittion
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

  </script>
  @endpush
</x-app-layout>