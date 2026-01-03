export const locales = {
    en: {
        welcome: {
            meta: {
                locale: 'en',
                title: 'Free Workout & Nutrition Plan Generator | fytrr',
                title_short: 'Create Free Workout & Nutrition Plan | fytrr',
                description:
                    'Create your free 7-day workout & nutrition plan as PDF. AI-powered, personalized & no signup required. Start now!',
                description_social:
                    'Free 7-day workout & nutrition plan as PDF. Generated with AI – personalized for muscle gain, fat loss or endurance.',
                theme_color: '#667eea',
                og: {
                    type: 'website',
                    site_name: 'fytrr.com',
                },
                twitter: {
                    card: 'summary_large_image',
                    site: '@fytrr',
                },
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
                    headline: "Let's start with the basics",
                    subline: 'This helps us personalize your plan',
                    error: 'Please select your gender',
                },
                personal: {
                    headline: 'Tell us about yourself',
                    subline:
                        'We need these details to calculate your calorie needs',
                    age: 'Age',
                    height: 'Height',
                    weight: 'Weight',
                    ageError: 'Please add your age',
                    heightError: 'Please add your height',
                    weightError: 'Please add your weight',
                },
                diet: {
                    headline: "What's your diet preference?",
                    subline: "We'll create meals that match your lifestyle",
                    label: 'Dietary Preferences',
                    placeholder: 'Select Diet Type',
                },
                activity: {
                    headline: 'How active are you?',
                    subline: 'This helps us understand your daily energy needs',
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
                    headline: "What's your main goal?",
                    subline: 'Your entire plan will be built around this',
                    label: 'Fitness Goal',
                    placeholder: 'Select Goal',
                    error: 'Please select your goal',
                },
                training: {
                    headline: 'Where do you prefer to train?',
                    subline:
                        "We'll design workouts based on your available equipment",
                    placeLabel: 'Training Location',
                    placeError: 'Please select your training place',
                    sessionsHeadline: 'How many days per week?',
                    sessionsSubline:
                        "More isn't always better - consistency is key",
                    sessionsLabel: 'Training Days',
                    sessionsError: 'Please set how often you want to train',
                    sessionsSuffix: 'times',
                    recommended: '(✨ recommended)',
                },
                final: {
                    headline: 'Almost there!',
                    subline: "We'll send your personalized plan to this email",
                    name: 'Your Name',
                    email: 'Email Address',
                    terms: 'I have taken note of the {termsLink}, {disclaimerLink} and {privacyLink} and accept them.',
                    termsLinkText: 'Terms & Conditions',
                    disclaimerLinkText: 'Disclaimer',
                    newsletter:
                        'I would like to subscribe to the free newsletter and regularly receive fitness tips, workout ideas, and exclusive content.',
                    submit: 'Generate plan',
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
                omnivore: 'Classic',
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
        workout_plan: {
            hero: {
                free_badge: '100% Free',
                weeks: 'Weeks',
                times_per_week: '× per Week',
                minutes: 'Minutes',
                level: 'Level',
                cta_button: 'Create Your Personalized Plan Now',
            },
            week_overview: {
                heading: 'Your Workout Plan in Detail',
                equipment_heading: 'Required Equipment',
                progression_heading: 'Progression',
                tips_heading: 'Tips for Maximum Success',
                sets: 'Sets',
                reps: 'Reps',
                rest: 'Rest',
            },
            faq: {
                heading: 'Frequently Asked Questions',
            },
            related: {
                heading: 'More Workout Plans',
                view_plan: 'View Plan',
            },
            cta: {
                heading: 'Ready for Your Personalized Plan?',
                description:
                    'Create your individual workout and nutrition plan now, based on your goals and requirements.',
                button: 'Create Plan',
            },
            why_it_works: {
                heading: 'Why This Plan Works',
                subheading:
                    'Science-based principles that guarantee your success',
                show_more: 'Show {count} more reasons',
                show_less: 'Show less',
            },
            common_mistakes: {
                heading: 'Avoid Common Mistakes',
                subheading:
                    'Avoid these typical beginner mistakes for better results',
                problem: 'Problem',
                consequence: 'Consequences',
                solution: 'Solution',
                example: 'Practical Example',
                show_more: 'Show {count} more mistakes',
                show_less: 'Show less',
            },
            author: {
                last_updated: 'Last updated',
                reviewed_by: 'Professionally reviewed by',
                about_content: 'About our content',
                disclosure:
                    'This workout plan was created with AI assistance and reviewed by certified professionals. All recommendations are based on current sports science and evidence-based principles.',
            },
        },
        form_panel: {
            submit: 'Continue',
        },
        legal: {
            disclaimer: {
                meta: {
                    title: 'Disclaimer & Health Information | fytrr',
                    description:
                        'Important health and safety information for using fytrr workout and nutrition plans.',
                },
                title: 'Disclaimer & Health Information',
                warning: {
                    title: 'Important Notice',
                    description:
                        'Please read these notices carefully before using the platform.',
                },
                sections: {
                    not_professional: {
                        title: 'Not a Replacement for Professional Advice',
                        subtitle:
                            'The workout and nutrition plans provided through fytrr do NOT replace consultation with qualified professionals such as doctors, nutritionists, personal trainers, or other health experts.',
                        content_1:
                            'The content is for general informational purposes only. It does not constitute medical diagnosis, treatment, or professional health advice.',
                        content_2:
                            'Always consult a doctor or qualified healthcare provider before starting any new workout or nutrition program.',
                    },
                    medical_conditions: {
                        title: 'Medical Conditions & Risks',
                        warning_title: 'Consult a doctor if you:',
                        conditions: [
                            'Have or had cardiovascular disease',
                            'Have diabetes or metabolic disorders',
                            'Have joint problems, back pain, or injuries',
                            'Are pregnant or nursing',
                            'Have or had eating disorders',
                            'Have high blood pressure or other chronic conditions',
                            'Take medications that may be affected by exercise or nutrition',
                            'Are over 40 and have not exercised in a long time',
                            'Are unsure about your health status',
                        ],
                        warning_note:
                            'Ignoring health problems can lead to serious injuries or health complications.',
                    },
                    ai_content: {
                        title: 'AI-Generated Content',
                        content_1:
                            'Workout and nutrition plans are created using Artificial Intelligence (AI). While we strive to deliver high-quality recommendations, AI systems can make mistakes or generate unsuitable suggestions.',
                        content_2:
                            'The AI does not know your complete medical history, current complaints, or individual physical limitations.',
                        content_3:
                            'Use common sense and listen to your body. If something is painful or feels wrong, STOP immediately.',
                    },
                    responsibility: {
                        title: 'Personal Responsibility',
                        content_1:
                            'You bear full responsibility for implementing the provided plans. You decide whether and how to follow the recommendations.',
                        content_2:
                            'The provider assumes no responsibility for:',
                        items: [
                            'Injuries resulting from training',
                            'Health problems from dietary changes',
                            'Allergic reactions to recommended foods',
                            'Unwanted weight changes',
                            'Worsening of existing health problems',
                            'Incorrect or incomplete AI recommendations',
                        ],
                    },
                    nutrition: {
                        title: 'Nutrition Information',
                        content:
                            'Nutrition plans are general suggestions and may not consider:',
                        items: [
                            'Food allergies or intolerances',
                            'Specific dietary requirements due to illnesses',
                            'Medication-food interactions',
                            'Individual metabolic characteristics',
                            'Cultural or religious dietary requirements',
                        ],
                        note: 'Inform yourself about suggested foods and check if they are suitable for you.',
                    },
                    training: {
                        title: 'Training Information',
                        content:
                            'Training exercises can lead to injuries if performed incorrectly.',
                        safety_title: 'Important Safety Tips:',
                        tips: [
                            'Start slowly and increase intensity gradually',
                            'Learn correct exercise execution (with trainer if needed)',
                            'Use appropriate weights - ego lifting leads to injuries',
                            'Warm up before training',
                            'Ensure adequate recovery',
                            'Stop immediately if in pain',
                            'Do not train when sick or overtired',
                        ],
                    },
                    no_guarantee: {
                        title: 'No Guarantee of Results',
                        content_1:
                            'The provider gives no guarantee or assurance for specific results (weight loss, muscle gain, performance improvement, etc.).',
                        content_2:
                            'Individual results vary greatly depending on factors such as genetics, starting condition, consistency, sleep, stress, and many other variables.',
                    },
                    liability: {
                        title: 'Limitation of Liability',
                        content_1:
                            'The provider is not liable for damages of any kind (including but not limited to direct, indirect, incidental, or consequential damages) arising from the use or inability to use the provided content.',
                        content_2: 'This applies in particular to:',
                        items: [
                            'Health damage or injuries',
                            'Worsening of existing conditions',
                            'Lack of or unexpected training results',
                            'Unwanted side effects from dietary changes',
                            'Errors in AI-generated recommendations',
                        ],
                        note: 'Statutory liability limitations remain unaffected (see Terms & Conditions).',
                    },
                    fda_disclaimer: {
                        title: 'FDA Disclaimer (United States)',
                        content_1:
                            'These statements have not been evaluated by the Food and Drug Administration (FDA).',
                        content_2:
                            'This service is not intended to diagnose, treat, cure, or prevent any disease or medical condition.',
                        content_3:
                            'The information provided is for educational and informational purposes only and should not be construed as medical advice.',
                    },

                    not_licensed: {
                        title: 'No Professional License',
                        content_1:
                            'The provider is NOT a licensed medical professional, registered dietitian, certified nutritionist, or certified personal trainer.',
                        content_2:
                            'This service does not provide professional medical advice, professional nutritional counseling, or professional fitness training.',
                        content_3:
                            'You should consult with licensed professionals before making any health, nutrition, or fitness decisions.',
                    },

                    emergency: {
                        title: 'Medical Emergency',
                        icon_warning: '🚨',
                        content_1:
                            'If you experience any of the following during exercise, STOP IMMEDIATELY and seek emergency medical care:',
                        symptoms: [
                            'Chest pain or pressure',
                            'Severe shortness of breath',
                            'Dizziness or lightheadedness',
                            'Unusual or irregular heartbeat',
                            'Severe joint or muscle pain',
                            'Nausea or vomiting',
                            'Any symptom that concerns you',
                        ],
                        content_2:
                            'Emergency numbers: 911 (US/Canada), 112 (EU), 999 (UK), or your local emergency number.',
                    },

                    jurisdiction: {
                        title: 'Jurisdiction & Applicable Law',
                        content_1:
                            'These terms and services are provided under German law. The provider is based in Germany and operates under German jurisdiction.',
                        content_2:
                            'For users outside Germany: Additional local laws, regulations, and consumer protection rights may apply in your country. It is your responsibility to ensure compliance with your local laws.',
                        content_3:
                            'By using this service from outside Germany, you acknowledge that you understand and accept that German law applies to this agreement.',
                    },

                    international_users: {
                        title: 'Notice for International Users',
                        content_1:
                            'This service is primarily designed for users in Germany and the European Union.',
                        content_2:
                            'If you are using this service from outside the EU:',
                        items: [
                            "Nutritional recommendations may not align with your country's dietary guidelines",
                            'Exercise recommendations may not comply with local fitness standards',
                            'Measurement units (metric) may differ from your local standards',
                            'Language and cultural context may be different',
                        ],
                        content_3:
                            'You are responsible for adapting any recommendations to your local context and regulations.',
                    },

                    no_medical_relationship: {
                        title: 'No Doctor-Patient or Professional Relationship',
                        content_1:
                            'Use of this service does NOT create a doctor-patient relationship, nutritionist-client relationship, or trainer-client relationship.',
                        content_2:
                            'The AI-generated recommendations are automated and not reviewed by licensed professionals.',
                        content_3:
                            'No confidentiality or professional duty of care exists between you and the provider.',
                    },

                    third_party_disclaimer: {
                        title: 'Third-Party Health Information',
                        content:
                            'This service may provide links or references to third-party health information, products, or services. The provider does not endorse, guarantee, or assume responsibility for any third-party content, recommendations, or services.',
                    },

                    assumption_of_risk: {
                        title: 'Assumption of Risk',
                        content_1:
                            'You expressly acknowledge and agree that use of this service and implementation of any recommendations is at your sole risk.',
                        content_2:
                            'You understand that physical exercise and dietary changes involve inherent risks including, but not limited to:',
                        risks: [
                            'Muscle strains, sprains, and tears',
                            'Joint injuries',
                            'Cardiovascular events',
                            'Metabolic complications',
                            'Allergic reactions',
                            'Nutritional deficiencies',
                            'Psychological distress',
                            'Death (in extreme cases)',
                        ],
                        content_3:
                            'You voluntarily assume all risks associated with using this service.',
                    },

                    indemnification: {
                        title: 'Indemnification',
                        content:
                            'You agree to indemnify, defend, and hold harmless the provider, its owners, employees, and affiliates from any claims, damages, losses, liabilities, and expenses (including legal fees) arising from your use of this service or violation of these terms.',
                    },

                    no_warranties: {
                        title: 'No Warranties',
                        content_1:
                            'This service is provided "AS IS" and "AS AVAILABLE" without warranties of any kind, either express or implied.',
                        content_2:
                            'The provider makes no warranties regarding:',
                        items: [
                            'Accuracy, reliability, or completeness of AI-generated content',
                            'Fitness for a particular purpose',
                            'Merchantability',
                            'Non-infringement',
                            'Uninterrupted or error-free service',
                            'Results or outcomes from using the service',
                        ],
                    },

                    // 11. SEVERABILITY
                    severability: {
                        title: 'Severability',
                        content:
                            'If any provision of this disclaimer is found to be unenforceable or invalid under applicable law, such unenforceability or invalidity shall not render this disclaimer unenforceable or invalid as a whole. Such provisions shall be deleted without affecting the remaining provisions.',
                    },

                    // 12. CHANGES TO DISCLAIMER
                    changes: {
                        title: 'Changes to This Disclaimer',
                        content_1:
                            'The provider reserves the right to modify this disclaimer at any time without prior notice.',
                        content_2:
                            'Your continued use of the service after changes constitutes acceptance of the modified disclaimer.',
                        content_3:
                            'It is your responsibility to review this disclaimer periodically.',
                    },
                    updates: {
                        title: 'Updates',
                        content:
                            'This disclaimer may be updated at any time. The current version is available on the website.',
                    },
                },
                confirmation: {
                    title: 'By using fytrr, you confirm that you:',
                    items: [
                        'Have read and understood these notices completely',
                        'Understand and accept the risks',
                        'Take full responsibility for your health',
                        'Will seek professional advice if in doubt',
                    ],
                },

                last_updated: 'Last updated: December 2024',
            },
            imprint: {
                meta: {
                    title: 'Imprint | fytrr',
                    description:
                        'Legal information and contact details for fytrr.',
                },
                title: 'Imprint',
            },
            data_privacy: {
                meta: {
                    title: 'Privacy Policy | fytrr',
                    description:
                        'Information about how we collect, use, and protect your personal data.',
                },
                title: 'Privacy Policy',
            },
            terms: {
                meta: {
                    title: 'Terms & Conditions | fytrr',
                    description:
                        'Terms and conditions for using fytrr services.',
                },
                title: 'Terms & Conditions',
            },
        },
        footer: {
            description:
                'Personalized AI workout and meal plans tailored to you.',
            copyright: '© {year} fytrr.com. Alle rights reserved',
        },
        set_password: {
            meta: {
                title: 'Set Your Password - Fytrr',
            },
            title: 'Set Your Password',
            subtitle: 'Your link will automatically open the Fytrr app',
            description:
                'Your link will automatically open the Fytrr app. If you don\'t have the app installed yet, download it below.',
            tip: {
                label: '👆 Tip:',
                text: 'If the app didn\'t open automatically, click the \"Open\" button in your browser.',
            },
            no_app: 'Don\'t have the app?',
            download_prompt: 'Download Fytrr to get started',
        },
    },
    de: {
        welcome: {
            meta: {
                locale: 'de',
                title: 'Trainingsplan & Ernährungsplan kostenlos erstellen | fytrr',
                title_short:
                    'Kostenlosen Trainings- & Ernährungsplan erstellen | fytrr',
                description:
                    'Erstelle deinen kostenlosen 7-Tage Trainings- & Ernährungsplan als PDF. KI-basiert, individuell & ohne Anmeldung. Jetzt starten!',
                description_social:
                    'Kostenloser 7-Tage Trainings- & Ernährungsplan als PDF. Erstellt mit KI – individuell für Muskelaufbau, Fettabbau oder Ausdauer.',
                theme_color: '#667eea',
                og: {
                    type: 'website',
                    site_name: 'fytrr.com',
                },
                twitter: {
                    card: 'summary_large_image',
                    site: '@fytrr',
                },
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
                    headline: 'Basics',
                    subline:
                        'So können wir deinen Trainingsplan optimal anpassen',
                    error: 'Bitte wähle dein Geschlecht',
                },
                personal: {
                    headline: 'Über dich',
                    subline:
                        'Diese Angaben nutzen wir, um deinen Kalorienbedarf exakt zu berechnen',
                    age: 'Alter',
                    height: 'Größe',
                    weight: 'Gewicht',
                    ageError: 'Bitte gib dein Alter an',
                    heightError: 'Bitte gib deine Größe an',
                    weightError: 'Bitte gib dein Gewicht an',
                },
                diet: {
                    headline: 'Ernährung',
                    subline:
                        'Wir erstellen Mahlzeiten, die zu deinem Alltag passen',
                    label: 'Ernährungspräferenzen',
                    placeholder: 'Diät-Typ auswählen',
                },
                activity: {
                    headline: 'Aktivität',
                    subline: 'So bestimmen wir deinen täglichen Energiebedarf',
                    activityLabel: 'Aktivitätslevel',
                    activityHint:
                        'Wie aktiv bist du an einem durchschnittlichen Tag?',
                    activityPlaceholder: 'Aktivitätslevel auswählen',
                    activityError: 'Bitte wähle dein Aktivitätslevel',
                    skillLabel: 'Erfahrungslevel',
                    skillHint: 'Wie viel Trainingserfahrung hast du?',
                    skillPlaceholder: 'Erfahrungslevel auswählen',
                    skillError: 'Bitte wähle dein Erfahrungslevel',
                },
                goal: {
                    headline: 'Dein Ziel',
                    subline:
                        'Dein kompletter Trainingsplan richtet sich danach',
                    label: 'Fitnessziel',
                    placeholder: 'Ziel auswählen',
                    error: 'Bitte wähle dein Ziel',
                },
                training: {
                    headline: 'Training',
                    subline:
                        'Wir passen die Übungen an dein verfügbares Equipment an',
                    placeLabel: 'Trainingsort',
                    placeError: 'Bitte wähle deinen Trainingsort',
                    sessionsHeadline: 'Trainingstage pro Woche',
                    sessionsSubline:
                        'Mehr ist nicht immer besser – Konstanz schlägt alles',
                    sessionsLabel: 'Trainingstage',
                    sessionsError:
                        'Bitte gib an, wie oft du trainieren möchtest',
                    sessionsSuffix: 'mal',
                    recommended: '(✨ empfohlen)',
                },
                final: {
                    headline: 'Fast geschafft!',
                    subline:
                        'Wir schicken dir deinen persönlichen Plan per E-Mail',
                    name: 'Dein Name',
                    email: 'E-Mail-Adresse',
                    terms: 'Ich habe von den {termsLink}, dem {disclaimerLink} und der {privacyLink} Kenntnis genommen und erkenne diese an.',
                    termsLinkText: 'AGB',
                    disclaimerLinkText: 'Haftungsausschluss',
                    privacyLinkText: 'Datenschutzerklärung',
                    newsletter:
                        'Ich möchte den kostenlosen Newsletter abonnieren und regelmäßig Fitness-Tipps, Trainingsideen und exklusive Inhalte erhalten.',
                    submit: 'Plan generieren',
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
                omnivore: 'Klassisch',
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
        workout_plan: {
            hero: {
                free_badge: '100% Kostenlos',
                weeks: 'Wochen',
                times_per_week: '× pro Woche',
                minutes: 'Minuten',
                level: 'Level',
                cta_button: 'Jetzt personalisierten Plan erstellen',
            },
            week_overview: {
                heading: 'Dein Trainingsplan im Detail',
                equipment_heading: 'Benötigtes Equipment',
                progression_heading: 'Progression',
                tips_heading: 'Tipps für maximalen Erfolg',
                sets: 'Sätze',
                reps: 'Wdh.',
                rest: 'Pause',
            },
            faq: {
                heading: 'Häufig gestellte Fragen',
            },
            related: {
                heading: 'Weitere Trainingspläne',
                view_plan: 'Plan ansehen',
            },
            cta: {
                heading: 'Bereit für deinen personalisierten Plan?',
                description:
                    'Erstelle jetzt deinen individuellen Trainings- und Ernährungsplan basierend auf deinen Zielen und Voraussetzungen.',
                button: 'Plan erstellen',
            },
            why_it_works: {
                heading: 'Warum dieser Plan funktioniert',
                subheading:
                    'Wissenschaftlich fundierte Prinzipien, die deinen Erfolg garantieren',
                show_more: '{count} weitere Gründe anzeigen',
                show_less: 'Weniger anzeigen',
            },
            common_mistakes: {
                heading: 'Häufige Fehler vermeiden',
                subheading:
                    'Vermeide diese typischen Anfängerfehler für bessere Ergebnisse',
                problem: 'Problem',
                consequence: 'Folgen',
                solution: 'Lösung',
                example: 'Praxis-Beispiel',
                show_more: '{count} weitere Fehler anzeigen',
                show_less: 'Weniger anzeigen',
            },
            author: {
                last_updated: 'Zuletzt aktualisiert',
                reviewed_by: 'Fachlich geprüft von',
                about_content: 'Über unsere Inhalte',
                disclosure:
                    'Dieser Trainingsplan wurde mit KI-Unterstützung erstellt und von zertifizierten Fachleuten überprüft. Alle Empfehlungen basieren auf aktueller Sportforschung und evidenzbasierten Prinzipien.',
            },
        },
        form_panel: {
            submit: 'Weiter',
        },
        legal: {
            disclaimer: {
                meta: {
                    title: 'Haftungsausschluss & Gesundheitshinweise | fytrr',
                    description:
                        'Wichtige Gesundheits- und Sicherheitsinformationen zur Nutzung von fytrr Trainings- und Ernährungsplänen.',
                },
                title: 'Haftungsausschluss & Gesundheitshinweise',
                warning: {
                    title: 'Wichtiger Hinweis',
                    description:
                        'Bitte lesen Sie diese Hinweise sorgfältig, bevor Sie die Plattform nutzen.',
                },
                sections: {
                    not_professional: {
                        title: 'Kein Ersatz für professionelle Beratung',
                        subtitle:
                            'Die über fytrr bereitgestellten Trainings- und Ernährungspläne ersetzen NICHT die Beratung durch qualifizierte Fachkräfte wie Ärzte, Ernährungsberater, Personal Trainer oder andere Gesundheitsexperten.',
                        content_1:
                            'Die Inhalte dienen ausschließlich zu allgemeinen Informationszwecken. Sie stellen keine medizinische Diagnose, Behandlung oder professionelle Gesundheitsberatung dar.',
                        content_2:
                            'Konsultieren Sie immer einen Arzt oder qualifizierten Gesundheitsdienstleister, bevor Sie mit einem neuen Trainings- oder Ernährungsprogramm beginnen.',
                    },
                    medical_conditions: {
                        title: 'Medizinische Vorerkrankungen & Risiken',
                        warning_title:
                            'Konsultieren Sie unbedingt einen Arzt, wenn Sie:',
                        conditions: [
                            'An Herz-Kreislauf-Erkrankungen leiden oder litten',
                            'Diabetes oder Stoffwechselstörungen haben',
                            'Gelenkprobleme, Rückenschmerzen oder Verletzungen haben',
                            'Schwanger sind oder stillen',
                            'Essstörungen haben oder hatten',
                            'Bluthochdruck oder andere chronische Erkrankungen haben',
                            'Medikamente einnehmen, die durch Sport oder Ernährung beeinflusst werden können',
                            'Über 40 Jahre alt sind und lange nicht trainiert haben',
                            'Sich unsicher über Ihren Gesundheitszustand sind',
                        ],
                        warning_note:
                            'Das Ignorieren gesundheitlicher Probleme kann zu schweren Verletzungen oder gesundheitlichen Komplikationen führen.',
                    },
                    ai_content: {
                        title: 'KI-generierte Inhalte',
                        content_1:
                            'Die Trainings- und Ernährungspläne werden mittels Künstlicher Intelligenz (KI) erstellt. Obwohl wir uns bemühen, qualitativ hochwertige Empfehlungen zu liefern, können KI-Systeme Fehler machen oder ungeeignete Vorschläge generieren.',
                        content_2:
                            'Die KI kennt nicht Ihre vollständige medizinische Geschichte, aktuelle Beschwerden oder individuellen körperlichen Einschränkungen.',
                        content_3:
                            'Nutzen Sie Ihren gesunden Menschenverstand und hören Sie auf Ihren Körper. Wenn etwas schmerzhaft ist oder sich falsch anfühlt, STOPPEN Sie sofort.',
                    },
                    responsibility: {
                        title: 'Eigenverantwortung',
                        content_1:
                            'Sie tragen die volle Verantwortung für die Umsetzung der bereitgestellten Pläne. Sie entscheiden selbst, ob und wie Sie die Empfehlungen befolgen.',
                        content_2:
                            'Der Anbieter übernimmt keine Verantwortung für:',
                        items: [
                            'Verletzungen, die durch Training entstehen',
                            'Gesundheitliche Probleme durch Ernährungsumstellungen',
                            'Allergische Reaktionen auf empfohlene Lebensmittel',
                            'Unerwünschte Gewichtsveränderungen',
                            'Verschlechterung bestehender Gesundheitsprobleme',
                            'Fehlerhafte oder unvollständige KI-Empfehlungen',
                        ],
                    },
                    nutrition: {
                        title: 'Ernährungshinweise',
                        content:
                            'Ernährungspläne sind allgemeine Vorschläge und berücksichtigen möglicherweise nicht:',
                        items: [
                            'Lebensmittelallergien oder Unverträglichkeiten',
                            'Spezifische diätetische Anforderungen aufgrund von Erkrankungen',
                            'Medikamenten-Nahrungsmittel-Interaktionen',
                            'Individuelle Stoffwechselbesonderheiten',
                            'Kulturelle oder religiöse Ernährungsanforderungen',
                        ],
                        note: 'Informieren Sie sich über die vorgeschlagenen Lebensmittel und prüfen Sie, ob diese für Sie geeignet sind.',
                    },
                    training: {
                        title: 'Trainingshinweise',
                        content:
                            'Trainingsübungen können bei falscher Ausführung zu Verletzungen führen.',
                        safety_title: 'Wichtige Sicherheitshinweise:',
                        tips: [
                            'Beginnen Sie langsam und steigern Sie die Intensität schrittweise',
                            'Lernen Sie die korrekte Übungsausführung (ggf. mit Trainer)',
                            'Verwenden Sie angemessene Gewichte - Ego-Lifting führt zu Verletzungen',
                            'Wärmen Sie sich vor dem Training auf',
                            'Achten Sie auf ausreichende Erholung',
                            'Stoppen Sie bei Schmerzen sofort',
                            'Trainieren Sie nicht krank oder übermüdet',
                        ],
                    },
                    no_guarantee: {
                        title: 'Keine Garantie für Ergebnisse',
                        content_1:
                            'Der Anbieter gibt keine Garantie oder Zusicherung für bestimmte Ergebnisse (Gewichtsverlust, Muskelaufbau, Leistungssteigerung etc.).',
                        content_2:
                            'Individuelle Ergebnisse variieren stark abhängig von Faktoren wie Genetik, Ausgangszustand, Konsistenz, Schlaf, Stress und vielen weiteren Variablen.',
                    },
                    liability: {
                        title: 'Haftungsausschluss',
                        content_1:
                            'Der Anbieter haftet nicht für Schäden jeglicher Art (einschließlich, aber nicht beschränkt auf direkte, indirekte, zufällige oder Folgeschäden), die aus der Nutzung oder der Unfähigkeit zur Nutzung der bereitgestellten Inhalte entstehen.',
                        content_2: 'Dies gilt insbesondere für:',
                        items: [
                            'Gesundheitliche Schäden oder Verletzungen',
                            'Verschlechterung bestehender Erkrankungen',
                            'Ausbleibende oder nicht erwartete Trainingsergebnisse',
                            'Unerwünschte Nebenwirkungen von Ernährungsumstellungen',
                            'Fehler in den KI-generierten Empfehlungen',
                        ],
                        note: 'Die gesetzlichen Haftungsbeschränkungen bleiben unberührt (siehe AGB).',
                    },
                    fda_disclaimer: {
                        title: 'FDA-Hinweis (USA)',
                        content_1:
                            'Diese Aussagen wurden nicht von der Food and Drug Administration (FDA) bewertet.',
                        content_2:
                            'Dieser Service ist nicht dazu bestimmt, Krankheiten oder medizinische Zustände zu diagnostizieren, zu behandeln, zu heilen oder zu verhindern.',
                        content_3:
                            'Die bereitgestellten Informationen dienen ausschließlich Bildungs- und Informationszwecken und sind nicht als medizinischer Rat zu verstehen.',
                    },

                    // 2. KEINE BERUFLICHE LIZENZ
                    not_licensed: {
                        title: 'Keine berufliche Qualifikation',
                        content_1:
                            'Der Anbieter ist KEIN zugelassener Arzt, Ernährungsberater, zertifizierter Trainer oder sonstiger Gesundheitsexperte.',
                        content_2:
                            'Dieser Service bietet keine professionelle medizinische Beratung, professionelle Ernährungsberatung oder professionelles Fitnesstraining.',
                        content_3:
                            'Sie sollten vor gesundheitlichen, ernährungsbezogenen oder fitnessbezogenen Entscheidungen lizenzierte Fachkräfte konsultieren.',
                    },

                    // 3. NOTFALL-WARNUNG
                    emergency: {
                        title: 'Medizinischer Notfall',
                        icon_warning: '🚨',
                        content_1:
                            'Wenn Sie während des Trainings eines der folgenden Symptome verspüren, STOPPEN Sie SOFORT und suchen Sie notärztliche Hilfe:',
                        symptoms: [
                            'Brustschmerzen oder Druckgefühl',
                            'Schwere Atemnot',
                            'Schwindel oder Benommenheit',
                            'Ungewöhnlicher oder unregelmäßiger Herzschlag',
                            'Starke Gelenk- oder Muskelschmerzen',
                            'Übelkeit oder Erbrechen',
                            'Jedes Symptom, das Sie beunruhigt',
                        ],
                        content_2:
                            'Notrufnummern: 112 (EU/Deutschland), 911 (USA/Kanada), 999 (UK) oder Ihre lokale Notrufnummer.',
                    },

                    // 4. GERICHTSSTAND & ANWENDBARES RECHT
                    jurisdiction: {
                        title: 'Gerichtsstand & Anwendbares Recht',
                        content_1:
                            'Diese Bedingungen und Leistungen werden nach deutschem Recht erbracht. Der Anbieter hat seinen Sitz in Deutschland und unterliegt der deutschen Gerichtsbarkeit.',
                        content_2:
                            'Für Nutzer außerhalb Deutschlands: Zusätzliche lokale Gesetze, Vorschriften und Verbraucherschutzrechte können in Ihrem Land gelten. Sie sind dafür verantwortlich, die Einhaltung Ihrer lokalen Gesetze sicherzustellen.',
                        content_3:
                            'Durch die Nutzung dieses Services außerhalb Deutschlands bestätigen Sie, dass Sie verstehen und akzeptieren, dass deutsches Recht für diese Vereinbarung gilt.',
                    },

                    // 5. HINWEIS FÜR INTERNATIONALE NUTZER
                    international_users: {
                        title: 'Hinweis für internationale Nutzer',
                        content_1:
                            'Dieser Service ist primär für Nutzer in Deutschland und der Europäischen Union konzipiert.',
                        content_2:
                            'Wenn Sie diesen Service außerhalb der EU nutzen:',
                        items: [
                            'Ernährungsempfehlungen entsprechen möglicherweise nicht den Ernährungsrichtlinien Ihres Landes',
                            'Trainingsempfehlungen entsprechen möglicherweise nicht lokalen Fitnessstandards',
                            'Maßeinheiten (metrisch) können von Ihren lokalen Standards abweichen',
                            'Sprache und kultureller Kontext können unterschiedlich sein',
                        ],
                        content_3:
                            'Sie sind dafür verantwortlich, Empfehlungen an Ihren lokalen Kontext und Ihre Vorschriften anzupassen.',
                    },

                    // 6. KEINE MEDIZINISCHE BEZIEHUNG
                    no_medical_relationship: {
                        title: 'Keine Arzt-Patienten- oder professionelle Beziehung',
                        content_1:
                            'Die Nutzung dieses Services begründet KEINE Arzt-Patienten-Beziehung, Ernährungsberater-Klienten-Beziehung oder Trainer-Klienten-Beziehung.',
                        content_2:
                            'Die KI-generierten Empfehlungen sind automatisiert und werden nicht von lizenzierten Fachkräften überprüft.',
                        content_3:
                            'Es besteht keine Vertraulichkeit oder professionelle Sorgfaltspflicht zwischen Ihnen und dem Anbieter.',
                    },

                    // 7. DRITTANBIETER-GESUNDHEITSINFORMATIONEN
                    third_party_disclaimer: {
                        title: 'Gesundheitsinformationen von Drittanbietern',
                        content:
                            'Dieser Service kann Links oder Verweise auf Gesundheitsinformationen, Produkte oder Dienstleistungen von Drittanbietern bereitstellen. Der Anbieter befürwortet, garantiert oder übernimmt keine Verantwortung für Inhalte, Empfehlungen oder Dienstleistungen von Drittanbietern.',
                    },

                    // 8. RISIKOÜBERNAHME
                    assumption_of_risk: {
                        title: 'Risikoübernahme',
                        content_1:
                            'Sie erkennen ausdrücklich an und stimmen zu, dass die Nutzung dieses Services und die Umsetzung von Empfehlungen auf Ihr alleiniges Risiko erfolgt.',
                        content_2:
                            'Sie verstehen, dass körperliche Bewegung und Ernährungsumstellungen inhärente Risiken beinhalten, einschließlich, aber nicht beschränkt auf:',
                        risks: [
                            'Muskelzerrungen, Verstauchungen und Risse',
                            'Gelenkverletzungen',
                            'Kardiovaskuläre Ereignisse',
                            'Stoffwechselkomplikationen',
                            'Allergische Reaktionen',
                            'Nährstoffmängel',
                            'Psychische Belastungen',
                            'Tod (in extremen Fällen)',
                        ],
                        content_3:
                            'Sie übernehmen freiwillig alle Risiken, die mit der Nutzung dieses Services verbunden sind.',
                    },

                    // 9. FREISTELLUNG
                    indemnification: {
                        title: 'Freistellung',
                        content:
                            'Sie verpflichten sich, den Anbieter, seine Eigentümer, Mitarbeiter und verbundenen Unternehmen von allen Ansprüchen, Schäden, Verlusten, Verbindlichkeiten und Kosten (einschließlich Anwaltskosten) freizustellen, die aus Ihrer Nutzung dieses Services oder der Verletzung dieser Bedingungen entstehen.',
                    },

                    // 10. KEINE GEWÄHRLEISTUNG
                    no_warranties: {
                        title: 'Keine Gewährleistung',
                        content_1:
                            'Dieser Service wird "WIE BESEHEN" und "WIE VERFÜGBAR" ohne Gewährleistungen jeglicher Art bereitgestellt, weder ausdrücklich noch stillschweigend.',
                        content_2:
                            'Der Anbieter übernimmt keine Gewährleistung für:',
                        items: [
                            'Richtigkeit, Zuverlässigkeit oder Vollständigkeit KI-generierter Inhalte',
                            'Eignung für einen bestimmten Zweck',
                            'Marktgängigkeit',
                            'Nichtverletzung von Rechten',
                            'Unterbrechungsfreien oder fehlerfreien Service',
                            'Ergebnisse oder Resultate aus der Nutzung des Services',
                        ],
                    },

                    // 11. SALVATORISCHE KLAUSEL
                    severability: {
                        title: 'Salvatorische Klausel',
                        content:
                            'Sollte eine Bestimmung dieses Haftungsausschlusses nach geltendem Recht nicht durchsetzbar oder ungültig sein, wird dadurch die Durchsetzbarkeit oder Gültigkeit des Haftungsausschlusses als Ganzes nicht berührt. Solche Bestimmungen werden gestrichen, ohne die verbleibenden Bestimmungen zu beeinträchtigen.',
                    },
                    updates: {
                        title: 'Aktualisierungen',
                        content:
                            'Dieser Haftungsausschluss kann jederzeit aktualisiert werden. Die jeweils aktuelle Version ist auf der Website einsehbar.',
                    },
                },
                confirmation: {
                    title: 'Durch die Nutzung von fytrr bestätigen Sie, dass Sie:',
                    items: [
                        'Diese Hinweise vollständig gelesen und verstanden haben',
                        'Die Risiken verstehen und akzeptieren',
                        'Die volle Verantwortung für Ihre Gesundheit übernehmen',
                        'Im Zweifelsfall professionelle Beratung einholen werden',
                    ],
                },

                changes: {
                    title: 'Änderungen dieses Haftungsausschlusses',
                    content_1:
                        'Der Anbieter behält sich das Recht vor, diesen Haftungsausschluss jederzeit ohne vorherige Ankündigung zu ändern.',
                    content_2:
                        'Ihre fortgesetzte Nutzung des Services nach Änderungen gilt als Annahme des geänderten Haftungsausschlusses.',
                    content_3:
                        'Es liegt in Ihrer Verantwortung, diesen Haftungsausschluss regelmäßig zu überprüfen.',
                },
                last_updated: 'Stand: Dezember 2024',
            },
            imprint: {
                meta: {
                    title: 'Impressum | fytrr',
                    description:
                        'Rechtliche Informationen und Kontaktdaten von fytrr.',
                },
                title: 'Impressum',
            },
            data_privacy: {
                meta: {
                    title: 'Datenschutzerklärung | fytrr',
                    description:
                        'Informationen darüber, wie wir Ihre personenbezogenen Daten erheben, verwenden und schützen.',
                },
                title: 'Datenschutzerklärung',
            },
            terms: {
                meta: {
                    title: 'AGB | fytrr',
                    description:
                        'Allgemeine Geschäftsbedingungen für die Nutzung von fytrr-Diensten.',
                },
                title: 'Allgemeine Geschäftsbedingungen',
            },
        },
        footer: {
            description:
                'KI-gestützte Trainings- und Ernährungspläne für deine Fitnessziele.',
            rights: 'All rights reserved.',
            copyright: '© {year} fytrr.com. Alle Rechte vorbehalten',
        },
        set_password: {
            meta: {
                title: 'Passwort festlegen - Fytrr',
            },
            title: 'Passwort festlegen',
            subtitle: 'Dein Link öffnet automatisch die Fytrr App',
            description:
                'Dein Link öffnet automatisch die Fytrr App. Falls du die App noch nicht installiert hast, lade sie unten herunter.',
            tip: {
                label: '👆 Tipp:',
                text: 'Falls sich die App nicht automatisch geöffnet hat, klicke auf den "Öffnen"-Button in deinem Browser.',
            },
            no_app: 'Hast du die App noch nicht?',
            download_prompt: 'Lade Fytrr herunter, um loszulegen',
        },
    },
} as const;

export type Locale = keyof typeof locales;
export type TranslationKeys = typeof locales.en;
