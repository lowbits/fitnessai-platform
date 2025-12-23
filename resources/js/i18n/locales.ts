export const locales = {
    en: {
        welcome: {
            meta: {
                title: 'Personalized AI Workout & Meal Plans in 60 Seconds | FitnessAI',
                description:
                    'Get a personalized AI workout and meal plan in 60 seconds. Tailored to your goals, fitness level, and equipment. Start free today.',
                structured_data: {
                    description:
                        'Personalized AI workout and meal plans tailored to your fitness goals, level, and available equipment.',
                },
            },
            hero: {
                title: 'Your Personalized',
                titleHighlight: 'Workout & Meal',
                titleEnd: 'Plans in 60 Seconds',
                subtitle:
                    'AI-generated fitness programs tailored to your goal, level, and equipment. Start your transformation today.',
            },
            convince: {
                research: {
                    headline: 'Based on Latest Research',
                    body: 'Frequent updates based on science ensure access to the latest training and nutrition recommendations.',
                },
                fast: {
                    headline: 'Get Plans Lightning Fast',
                    body: 'AI analyzes your data instantly, delivering personalized plans faster and more accurately than traditional methods.',
                },
                goals: {
                    headline: 'Reach Your Goals Faster',
                    body: 'Achieve your fitness goals with AI-generated workout & nutrition plans that adapt to your progress.',
                },
            },
            cta: {
                title: 'Start Your AI-Powered Workout Today!',
                subtitle:
                    'Get a complimentary AI-generated workout & nutrition plan designed specifically for you.',
            },
            features: {
                label: 'Features',
                title: 'Transform Your Fitness with AI-Powered Plans',
                subtitle:
                    'Explore cutting-edge features designed to optimize your workouts, nutrition, and overall progress.',
                plans: {
                    headline: 'Create Individual Plans',
                    body: 'Design unique workout and nutrition plans tailored to your specific goals and needs.',
                },
                shopping: {
                    headline: 'Shop with Ease',
                    body: 'Generate shopping lists automatically from your personalized nutrition plan.',
                },
                coach: {
                    headline: 'Get Instant Advice',
                    body: 'Receive real-time guidance from your AI Coach for on-the-spot support during workouts.',
                },
            },
            faq: {
                title: 'Frequently asked questions',
                contact: 'If you have anything else you want to ask,',
                contactLink: 'reach out to us',
                accuracy: {
                    question: 'How accurate is an AI-generated nutrition plan?',
                    answer: 'The accuracy of an AI-generated nutrition plan depends on the quality of data and algorithms used. With proper inputs, our AI provides highly accurate, personalized recommendations based on your individual needs, goals, and current fitness level. Our system is continuously updated with the latest research to ensure optimal results.',
                },
                dietitian: {
                    question:
                        'Will an AI-generated plan replace the need for a dietitian?',
                    answer: 'No, our AI-generated plans should complement, not replace, professional advice from licensed dietitians or healthcare providers. While our AI provides personalized recommendations and daily guidance, we always recommend consulting with healthcare professionals before making significant dietary or fitness changes, especially if you have specific health conditions.',
                },
                safety: {
                    question: 'How can I be sure the plan is safe for me?',
                    answer: 'Before starting any new fitness or nutrition program, we recommend consulting with your healthcare provider, especially if you have existing health conditions, injuries, or dietary restrictions. Our AI takes your input into account, but professional medical advice is always recommended for your safety.',
                },
                restrictions: {
                    question:
                        'Can the AI accommodate special diets or restrictions?',
                    answer: 'Yes! Our AI can accommodate special dietary needs including allergies, food preferences, vegetarian/vegan diets, religious restrictions, and more. Simply provide accurate information during the setup process. However, for complex medical conditions or severe allergies, please consult with a healthcare professional to ensure the plan is safe and appropriate for you.',
                },
            },
            footer: {
                imprint: 'Imprint',
            },
        },
        form: {
            validation: {
                genderRequired: 'Select your gender',
                ageRequired: 'Enter your age',
                heightRequired: 'Enter your height',
                weightRequired: 'Enter your weight',
                activityLevelRequired: 'Select your activity level',
                skillLevelRequired: 'Select your experience level',
                bodyGoalRequired: 'Select your goal',
                trainingPlaceRequired: 'Select your training location',
                trainingSessionsRequired: 'Choose how often you want to train',
            },
            success: {
                title: 'Almost there',
                thankYou: 'Your details have been saved.',
                emailSent: "We've just sent a verification email to:",
                whatNext: 'Here’s what happens next:',
                steps: {
                    inbox: 'Open your email inbox (and check spam if needed)',
                    verify: 'Verify your email address using the link',
                    generate: 'We’ll then prepare your personal plan',
                },
                generationTime: 'Estimated time',
                generationText:
                    'Your full {days}-day training and meal plan will be ready shortly after verification.',
                minutes: '3–5 minutes',
                didntReceive: 'Can’t find the email?',
                checkSpam: 'Check your spam folder or contact us at',
                linkValid: 'The verification link is valid for {hours}.',
                hours: '24 hours',
            },
            steps: {
                gender: {
                    label: 'Gender',
                    error: 'Please select your gender',
                },
                personal: {
                    age: 'Age',
                    height: 'Height',
                    weight: 'Weight',
                    ageError: 'Please add your age',
                    heightError: 'Please add your height',
                    weightError: 'Please add your weight',
                },
                diet: {
                    label: 'Dietary Preferences',
                    placeholder: 'Select Diet Type',
                },
                activity: {
                    activityLabel: 'Activity Level',
                    activityHint: "What's your activity on an average day?",
                    activityPlaceholder: 'Select Activity Level',
                    activityError: 'Please select your activity level',
                    skillLabel: 'Skill Level',
                    skillHint: 'How experienced are you in fitness?',
                    skillPlaceholder: 'Select Skill Level',
                    skillError: 'Please select your skill level',
                },
                goal: {
                    label: 'Fitness Goal',
                    placeholder: 'Select Goal',
                    error: 'Please select your goal',
                },
                training: {
                    placeLabel: 'Where do you train?',
                    placeError: 'Please select your training place',
                    sessionsLabel: 'How often do you train?',
                    sessionsError: 'Please set how often you want to train',
                    sessionsSuffix: 'times',
                    recommended: '(✨ recommended)',
                },
                final: {
                    name: 'Your Name',
                    email: 'Email Address',
                    terms: 'I agree to the fitnessAI.me User Agreement and Privacy Policy.',
                    newsletter:
                        "By checking, you'll sign up for our newsletter and receive your first",
                    newsletterHighlight: 'nutrition and workout plan for free',
                    submit: 'Generate',
                    submitting: 'Generating...',
                },
            },
            devHelper: 'Set Form',
        },
        emailVerification: {
            generating: {
                verified: 'Email Verified!',
                welcome: 'Welcome, {name}!',
                title: 'Generating Your Personalized Plan',
                description:
                    'This may take a few minutes. You can close this page, we will inform you when your plan is ready.',
                complete: '✅ Your plan is ready! Check your emails...',
                hasFailures:
                    "⚠️ Some items failed to generate. We'll continue with what we have.",
                generating: 'Generating your personalized plan...',
                loadingStatus: 'Loading status...',
                planLabel: 'Plan:',
                startDateLabel: 'Start Date:',
            },
            expired: {
                title: 'Verification Link Expired',
                description:
                    'This verification link has expired. Verification links are valid for 24 hours.',
                whatToDo: 'What to do:',
                steps: {
                    contact:
                        'Contact us at {email} to request a new verification email',
                    include: 'Include your email address in the message',
                },
                backHome: 'Back to Home',
            },
            invalid: {
                title: 'Invalid Verification Link',
                description:
                    'This verification link is invalid or has already been used.',
                possibleReasons: 'Possible reasons:',
                reasons: {
                    alreadyVerified: 'Your email is already verified',
                    invalidLink: 'The link is malformed or incorrect',
                    alreadyUsed: 'This link has already been used',
                },
                whatToDo: 'What to do:',
                steps: {
                    tryLogin:
                        'Try logging in - your email might already be verified',
                    contact:
                        'Contact us at {email} if you continue to have issues',
                },
                backHome: 'Back to Home',
            },
        },
        enums: {
            gender: {
                male: 'Male',
                female: 'Female',
                other: 'Other',
                prefer_not_to_say: 'Prefer not to say',
            },
            bodyGoal: {
                muscle_gain: 'Muscle Gain',
                weight_loss: 'Weight Loss',
                maintenance: 'Maintenance',
                endurance: 'Endurance',
                strength: 'Strength',
            },
            skillLevel: {
                beginner: 'Beginner',
                intermediate: 'Intermediate',
                advanced: 'Advanced',
                elite: 'Elite',
            },
            activityLevel: {
                mainly_sitting: 'Mainly Sitting',
                mainly_standing: 'Mainly Standing',
                mainly_walking: 'Mainly Walking',
                hard_working: 'Hard Physical Work',
            },
            trainingPlace: {
                gym: 'Gym',
                home: 'Home',
                outdoor: 'Outdoor',
                no_preference: 'No Preference',
            },
            dietType: {
                omnivore: 'Omnivore',
                vegetarian: 'Vegetarian',
                pescatarian: 'Pescatarian',
                vegan: 'Vegan',
                high_protein: 'High Protein',
                low_carb: 'Low Carb',
                ketogenic: 'Ketogenic',
                paleo: 'Paleo',
                mediterranean: 'Mediterranean',
                intermittent_fasting: 'Intermittent Fasting',
            },
        },
        form_panel: {
            submit: 'Continue',
        },
    },
    de: {
        welcome: {
            meta: {
                title: 'Trainingsplan & Ernährungsplan kostenlos erstellen | FitnessAI',
                description:
                    'Erstelle deinen kostenlosen 7-Tage Trainings- & Ernährungsplan als PDF. KI-basiert, individuell & ohne Anmeldung. Jetzt starten!',
                structured_data: {
                    description:
                        'Erstelle deinen kostenlosen 7-Tage Trainings- & Ernährungsplan als PDF. KI-basiert, individuell & ohne Anmeldung.',
                },
            },
            hero: {
                title: 'Trainings- & Ernährungsplan',
                titleHighlight: 'kostenlos erstellen ',
                titleEnd: '– in 60 Sekunden',
                subtitle:
                    'Erhalte deinen persönlichen 7-Tage Trainings- & Ernährungsplan als PDF – KI-basiert und individuell nach Ziel, Trainingslevel & Equipment.',
            },
            convince: {
                research: {
                    headline: 'Basiert auf neuester Forschung',
                    body: 'Häufige Updates basierend auf wissenschaftlichen Erkenntnissen garantieren Zugang zu den neuesten Trainings- und Ernährungsempfehlungen.',
                },
                fast: {
                    headline: 'Erhalte Pläne blitzschnell',
                    body: 'Die KI analysiert deine Daten sofort und liefert personalisierte Pläne schneller und genauer als traditionelle Methoden.',
                },
                goals: {
                    headline: 'Erreiche deine Ziele schneller',
                    body: 'Erreiche deine Fitnessziele mit KI-generierten Trainings- und Ernährungsplänen, die sich deinem Fortschritt anpassen.',
                },
            },
            cta: {
                title: 'Starte noch heute dein KI-gestütztes Training!',
                subtitle:
                    'Erhalte einen kostenlosen KI-generierten Trainings- und Ernährungsplan, speziell für dich entwickelt.',
            },
            features: {
                label: 'Funktionen',
                title: 'Transformiere deine Fitness mit KI-gestützten Plänen',
                subtitle:
                    'Entdecke modernste Funktionen, die deine Trainings, Ernährung und deinen gesamten Fortschritt optimieren.',
                plans: {
                    headline: 'Erstelle individuelle Pläne',
                    body: 'Entwickle einzigartige Trainings- und Ernährungspläne, die auf deine spezifischen Ziele und Bedürfnisse zugeschnitten sind.',
                },
                shopping: {
                    headline: 'Einkaufen leicht gemacht',
                    body: 'Generiere automatisch Einkaufslisten aus deinem personalisierten Ernährungsplan.',
                },
                coach: {
                    headline: 'Erhalte sofortige Beratung',
                    body: 'Erhalte Echtzeit-Anleitungen von deinem KI-Coach für Unterstützung direkt während des Trainings.',
                },
            },
            faq: {
                title: 'Häufig gestellte Fragen',
                contact: 'Wenn du weitere Fragen hast,',
                contactLink: 'kontaktiere uns',
                accuracy: {
                    question:
                        'Wie genau ist ein KI-generierter Ernährungsplan?',
                    answer: 'Die Genauigkeit eines KI-generierten Ernährungsplans hängt von der Qualität der verwendeten Daten und Algorithmen ab. Mit korrekten Eingaben liefert unsere KI hochpräzise, personalisierte Empfehlungen basierend auf deinen individuellen Bedürfnissen, Zielen und deinem aktuellen Fitnesslevel. Unser System wird kontinuierlich mit den neuesten Forschungsergebnissen aktualisiert, um optimale Ergebnisse zu gewährleisten.',
                },
                dietitian: {
                    question:
                        'Ersetzt ein KI-generierter Plan die Notwendigkeit eines Ernährungsberaters?',
                    answer: 'Nein, unsere KI-generierten Pläne sollten professionellen Rat von lizenzierten Ernährungsberatern oder medizinischen Fachkräften ergänzen, nicht ersetzen. Während unsere KI personalisierte Empfehlungen und tägliche Anleitungen bietet, empfehlen wir immer die Konsultation von medizinischen Fachleuten, bevor du bedeutende Ernährungs- oder Fitnessänderungen vornimmst, besonders wenn du spezifische Gesundheitszustände hast.',
                },
                safety: {
                    question:
                        'Wie kann ich sicher sein, dass der Plan für mich sicher ist?',
                    answer: 'Bevor du ein neues Fitness- oder Ernährungsprogramm startest, empfehlen wir die Rücksprache mit deinem Arzt, besonders wenn du bestehende Gesundheitsprobleme, Verletzungen oder Ernährungseinschränkungen hast. Unsere KI berücksichtigt deine Eingaben, aber professioneller medizinischer Rat wird immer für deine Sicherheit empfohlen.',
                },
                restrictions: {
                    question:
                        'Kann die KI spezielle Diäten oder Einschränkungen berücksichtigen?',
                    answer: 'Ja! Unsere KI kann spezielle Ernährungsbedürfnisse berücksichtigen, einschließlich Allergien, Lebensmittelpräferenzen, vegetarischer/veganer Ernährung, religiöser Einschränkungen und mehr. Gib einfach genaue Informationen während des Einrichtungsprozesses an. Bei komplexen medizinischen Zuständen oder schweren Allergien konsultiere bitte einen medizinischen Fachmann, um sicherzustellen, dass der Plan sicher und angemessen für dich ist.',
                },
            },
            footer: {
                imprint: 'Impressum',
            },
        },
        form: {
            validation: {
                genderRequired: 'Geschlecht auswählen',
                ageRequired: 'Alter angeben',
                heightRequired: 'Größe angeben',
                weightRequired: 'Gewicht angeben',
                activityLevelRequired: 'Aktivitätslevel auswählen',
                skillLevelRequired: 'Erfahrungslevel auswählen',
                bodyGoalRequired: 'Ziel auswählen',
                trainingPlaceRequired: 'Trainingsort auswählen',
                trainingSessionsRequired: 'Trainingshäufigkeit auswählen',
            },
            success: {
                title: 'Fast geschafft',
                thankYou: 'Deine Angaben sind gespeichert.',
                emailSent:
                    'Wir haben dir gerade eine Bestätigungs-E-Mail gesendet an:',
                whatNext: 'So geht es weiter:',
                steps: {
                    inbox: 'Öffne dein E-Mail-Postfach (ggf. auch den Spam-Ordner)',
                    verify: 'Bestätige deine E-Mail-Adresse über den Link',
                    generate: 'Danach erstellen wir deinen persönlichen Plan',
                },
                generationTime: 'Dauer',
                generationText:
                    'Dein kompletter {days}-Tage-Plan mit Training & Ernährung ist kurz nach der Bestätigung für dich bereit.',
                minutes: '3–5 Minuten',
                didntReceive: 'Keine E-Mail gefunden?',
                checkSpam:
                    'Bitte prüfe deinen Spam-Ordner oder kontaktiere uns unter',
                linkValid: 'Der Bestätigungslink ist {hours} gültig.',
                hours: '24 Stunden',
            },
            steps: {
                gender: {
                    label: 'Geschlecht',
                    error: 'Bitte wähle dein Geschlecht',
                },
                personal: {
                    age: 'Alter',
                    height: 'Größe',
                    weight: 'Gewicht',
                    ageError: 'Bitte gib dein Alter an',
                    heightError: 'Bitte gib deine Größe an',
                    weightError: 'Bitte gib dein Gewicht an',
                },
                diet: {
                    label: 'Ernährungspräferenzen',
                    placeholder: 'Diät-Typ auswählen',
                },
                activity: {
                    activityLabel: 'Aktivitätslevel',
                    activityHint:
                        'Wie aktiv bist du an einem durchschnittlichen Tag?',
                    activityPlaceholder: 'Aktivitätslevel auswählen',
                    activityError: 'Bitte wähle dein Aktivitätslevel',
                    skillLabel: 'Erfahrungslevel',
                    skillHint: 'Wie erfahren bist du im Fitness-Bereich?',
                    skillPlaceholder: 'Erfahrungslevel auswählen',
                    skillError: 'Bitte wähle dein Erfahrungslevel',
                },
                goal: {
                    label: 'Fitnessziel',
                    placeholder: 'Ziel auswählen',
                    error: 'Bitte wähle dein Ziel',
                },
                training: {
                    placeLabel: 'Wo trainierst du?',
                    placeError: 'Bitte wähle deinen Trainingsort',
                    sessionsLabel: 'Wie oft trainierst du?',
                    sessionsError:
                        'Bitte gib an, wie oft du trainieren möchtest',
                    sessionsSuffix: 'mal',
                    recommended: '(✨ empfohlen)',
                },
                final: {
                    name: 'Dein Name',
                    email: 'E-Mail-Adresse',
                    terms: 'Ich stimme den Nutzungsbedingungen und der Datenschutzerklärung von fitnessAI.me zu.',
                    newsletter:
                        'Durch Aktivieren meldest du dich für unseren Newsletter an und erhältst deinen ersten',
                    newsletterHighlight:
                        'Ernährungs- und Trainingsplan kostenlos',
                    submit: 'Generieren',
                    submitting: 'Wird generiert...',
                },
            },
            devHelper: 'Formular füllen',
        },
        emailVerification: {
            generating: {
                verified: 'E-Mail verifiziert!',
                welcome: 'Willkommen, {name}!',
                title: 'Dein personalisierter Plan wird erstellt',
                description:
                    'Dies kann einige Minuten dauern. Du kannst diese Seite schließen, wir informieren dich, wenn dein Plan fertig ist.',
                complete: '✅ Dein Plan ist fertig! Prüfe deine E-Mails...',
                hasFailures:
                    '⚠️ Einige Elemente konnten nicht generiert werden. Wir fahren mit dem fort, was wir haben.',
                generating: 'Dein personalisierter Plan wird erstellt...',
                loadingStatus: 'Status wird geladen...',
                planLabel: 'Plan:',
                startDateLabel: 'Startdatum:',
            },
            expired: {
                title: 'Bestätigungslink abgelaufen',
                description:
                    'Dieser Bestätigungslink ist abgelaufen. Bestätigungslinks sind 24 Stunden gültig.',
                whatToDo: 'Was tun:',
                steps: {
                    contact:
                        'Kontaktiere uns unter {email}, um eine neue Bestätigungs-E-Mail anzufordern',
                    include: 'Gib deine E-Mail-Adresse in der Nachricht an',
                },
                backHome: 'Zurück zur Startseite',
            },
            invalid: {
                title: 'Ungültiger Bestätigungslink',
                description:
                    'Dieser Bestätigungslink ist ungültig oder wurde bereits verwendet.',
                possibleReasons: 'Mögliche Gründe:',
                reasons: {
                    alreadyVerified: 'Deine E-Mail ist bereits verifiziert',
                    invalidLink: 'Der Link ist fehlerhaft oder falsch',
                    alreadyUsed: 'Dieser Link wurde bereits verwendet',
                },
                whatToDo: 'Was tun:',
                steps: {
                    tryLogin:
                        'Versuche dich anzumelden - deine E-Mail könnte bereits verifiziert sein',
                    contact:
                        'Kontaktiere uns unter {email}, falls du weiterhin Probleme hast',
                },
                backHome: 'Zurück zur Startseite',
            },
        },
        enums: {
            gender: {
                male: 'Männlich',
                female: 'Weiblich',
                other: 'Divers',
                prefer_not_to_say: 'Keine Angabe',
            },
            bodyGoal: {
                muscle_gain: 'Muskelaufbau',
                weight_loss: 'Gewichtsverlust',
                maintenance: 'Erhaltung',
                endurance: 'Ausdauer',
                strength: 'Kraft',
            },
            skillLevel: {
                beginner: 'Anfänger',
                intermediate: 'Fortgeschritten',
                advanced: 'Erfahren',
                elite: 'Elite',
            },
            activityLevel: {
                mainly_sitting: 'Überwiegend sitzend',
                mainly_standing: 'Überwiegend stehend',
                mainly_walking: 'Überwiegend gehend',
                hard_working: 'Körperlich anstrengend tätig',
            },
            trainingPlace: {
                gym: 'Fitnessstudio',
                home: 'Zuhause',
                outdoor: 'Draußen',
                no_preference: 'Keine Präferenz',
            },
            dietType: {
                omnivore: 'Omnivor',
                vegetarian: 'Vegetarisch',
                pescatarian: 'Pescetarisch',
                vegan: 'Vegan',
                high_protein: 'Proteinreich',
                low_carb: 'Kohlenhydratarm',
                ketogenic: 'Ketogen',
                paleo: 'Paleo',
                mediterranean: 'Mediterran',
                intermittent_fasting: 'Intervallfasten',
            },
        },
        form_panel: {
            submit: 'Weiter',
        },
    },
} as const;

export type Locale = keyof typeof locales;
export type TranslationKeys = typeof locales.en;
