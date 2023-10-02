'use strict'

const validation = new JustValidate('#registration-form')

validation
  .addField('#name', [
    {
      rule: 'required',
    },
  ])
  .addField('#email', [
    {
      rule: 'email',
    },
    {
      rule: 'required',
    },
    {
      validator: (value) => async () => {
        const response = await fetch(
          'validate-email.php?email=' + encodeURIComponent(value)
        )
        const data = await response.json()

        return data.is_available
      },
      errorMessage: 'Email already exist',
    },
  ])
  .addField('#password', [
    {
      rule: 'required',
    },
    {
      rule: 'password',
    },
  ])
  .addField('#confirm-password', [
    {
      validator: (value, fields) => {
        return value === fields['#password'].elem.value
      },
      errorMessage: 'Passwords should match',
    },
  ])
  .onSuccess(() => {
    document.querySelector('#registration-form').submit()
  })
